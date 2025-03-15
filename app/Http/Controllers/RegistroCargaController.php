<?php

namespace App\Http\Controllers;

use App\Models\RegistroCarga;
use App\Models\Vehiculo;
use App\Models\Surtidor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegistroCargaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Verificar si el usuario es administrador
        if ($user->rol->nombre === 'admin') {
            // Si es admin, mostrar todos los registros de carga
            $registrosCarga = RegistroCarga::with(['usuario', 'vehiculo', 'surtidor'])
                ->orderBy('fecha_carga', 'desc')
                ->get();
        } else {
            // Si no es admin, mostrar solo sus propios registros
            $registrosCarga = RegistroCarga::with(['vehiculo', 'surtidor'])
                ->where('usuario_id', $user->id)
                ->orderBy('fecha_carga', 'desc')
                ->get();
        }
        
        return response()->json($registrosCarga);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'surtidor_id' => 'required|exists:surtidores,id',
            'fecha_carga' => 'required|date',
            'cantidad_litros' => 'required|numeric|min:0'
        ]);
        
        // Verificar que el vehículo pertenezca al usuario
        $vehiculo = Vehiculo::findOrFail($validatedData['vehiculo_id']);
        if ($vehiculo->usuario_id != $validatedData['usuario_id']) {
            return response()->json([
                'error' => 'El vehículo no pertenece al usuario especificado'
            ], 400);
        }
        
        // Verificar que el surtidor tenga suficiente combustible
        $surtidor = Surtidor::findOrFail($validatedData['surtidor_id']);
        if (!$surtidor->haySuficienteCombustible($validatedData['cantidad_litros'])) {
            return response()->json([
                'error' => 'No hay suficiente combustible en el surtidor'
            ], 400);
        }

        // Convertir ISO 8601 timestamp a formato MySQL datetime
        $fechaCarga = Carbon::parse($request->input('fecha_carga'))->format('Y-m-d H:i:s');
        
        // Generar código QR único
        $codigoQR = 'QR-' . uniqid() . '-' . $validatedData['usuario_id'] . '-' . $validatedData['vehiculo_id'];
        
        // Crear el registro de carga
        $registroCarga = RegistroCarga::create([
            'usuario_id' => $validatedData['usuario_id'],
            'vehiculo_id' => $validatedData['vehiculo_id'],
            'surtidor_id' => $validatedData['surtidor_id'],
            'fecha_carga' => $fechaCarga,
            'cantidad_litros' => $validatedData['cantidad_litros'],
            'codigo_qr' => $codigoQR,
            'qr_habilitado' => false,
            'fecha_proxima_habilitacion' => Carbon::parse($fechaCarga)->addDays(5)
        ]);
        
        // Reducir el combustible del surtidor
        $surtidor->reducirCombustible($validatedData['cantidad_litros']);
        
        return response()->json([
            'message' => 'Registro de carga creado exitosamente',
            'registro' => $registroCarga
        ], 201);
    }
    
    public function checkQRStatus($usuarioId)
    {
        // Verificar si hay algún registro de carga reciente con QR deshabilitado
        $ultimoRegistro = RegistroCarga::where('usuario_id', $usuarioId)
            ->where('qr_habilitado', false)
            ->where('fecha_carga', '>', now()->subDays(7))
            ->first();
        
        return response()->json([
            'qr_habilitado' => $ultimoRegistro ? false : true,
            'fecha_proxima_habilitacion' => $ultimoRegistro ? $ultimoRegistro->fecha_proxima_habilitacion : null
        ]);
    }
    
    public function marcarQR(Request $request)
    {
        $validated = $request->validate([
            'codigo_qr' => 'required|string|exists:registros_carga,codigo_qr',
        ]);
        
        $registroCarga = RegistroCarga::where('codigo_qr', $validated['codigo_qr'])->first();
        
        if (!$registroCarga) {
            return response()->json([
                'error' => 'Código QR no encontrado'
            ], 404);
        }
        
        // Verificar si el QR está habilitado
        if (!$registroCarga->qr_habilitado && 
            $registroCarga->fecha_proxima_habilitacion > now()) {
            return response()->json([
                'error' => 'Código QR deshabilitado',
                'fecha_proxima_habilitacion' => $registroCarga->fecha_proxima_habilitacion
            ], 400);
        }
        
        // Marcar el QR como usado
        $registroCarga->qr_habilitado = false;
        $registroCarga->fecha_proxima_habilitacion = Carbon::now()->addDays(5);
        $registroCarga->save();
        
        return response()->json([
            'message' => 'QR marcado como escaneado y registrado',
            'registro' => $registroCarga,
            'timestamp' => now()->toDateTimeString()
        ], 200);
    }
    
    public function habilitarQR($id)
    {
        $registroCarga = RegistroCarga::findOrFail($id);
        $registroCarga->qr_habilitado = true;
        $registroCarga->fecha_proxima_habilitacion = null;
        $registroCarga->save();
        
        return response()->json([
            'message' => 'QR habilitado manualmente',
            'registro' => $registroCarga
        ]);
    }

    public function show($id)
    {
        return response()->json(RegistroCarga::with(['usuario', 'vehiculo', 'surtidor'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $registroCarga = RegistroCarga::findOrFail($id);
        
        $validatedData = $request->validate([
            'usuario_id' => 'sometimes|exists:usuarios,id',
            'vehiculo_id' => 'sometimes|exists:vehiculos,id',
            'surtidor_id' => 'sometimes|exists:surtidores,id',
            'fecha_carga' => 'sometimes|date',
            'cantidad_litros' => 'sometimes|numeric|min:0',
            'qr_habilitado' => 'sometimes|boolean',
            'fecha_proxima_habilitacion' => 'sometimes|date|nullable'
        ]);
        
        $registroCarga->update($validatedData);
        
        return response()->json([
            'message' => 'Registro de carga actualizado',
            'registro' => $registroCarga
        ]);
    }

    public function destroy($id)
    {
        $registroCarga = RegistroCarga::find($id);
        if (!$registroCarga) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }
        $registroCarga->delete();
        return response()->json(['message' => 'Registro de carga eliminado'], 200);
    }
}