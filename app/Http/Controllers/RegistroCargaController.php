<?php

namespace App\Http\Controllers;

use App\Models\RegistroCarga;
use Illuminate\Http\Request;
use Carbon\Carbon;
class RegistroCargaController extends Controller
{
    public function index()
    {
        // Verificación explícita de autenticación
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Usuario no autenticado',
                'message' => 'Debe iniciar sesión para ver los registros'
            ], 401);
        }
    
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'No se pudo encontrar el usuario',
                'message' => 'Hay un problema con la autenticación'
            ], 401);
        }
    
        $registrosCarga = RegistroCarga::with('usuario')
            ->where('usuario_id', $user->id)
            ->orderBy('fecha_carga', 'desc')
            ->get();
        
        return response()->json($registrosCarga);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'fecha_carga' => 'required|date',
            'qrHabilitado' => 'boolean'
        ]);
    
        // Convert ISO 8601 timestamp to MySQL datetime format
        $fechaCarga = \Carbon\Carbon::parse($request->input('fecha_carga'))->format('Y-m-d H:i:s');
    
        $registroCarga = RegistroCarga::create([
            'usuario_id' => $validatedData['usuario_id'],
            'fecha_carga' => $fechaCarga,
            'qrHabilitado' => false
        ]);
    
        return response()->json($registroCarga, 201);
    }
    
    public function checkQRStatus($usuarioId)
    {
        // Check if there's a recent QR scan that's still disabled
        $ultimoRegistro = RegistroCarga::where('usuario_id', $usuarioId)
            ->where('qrHabilitado', false)
            ->where('fecha_carga', '>', now()->subDays(7))
            ->first();
    
        return response()->json([
            'qrHabilitado' => $ultimoRegistro ? false : true
        ]);
    }
    public function marcarQR(Request $request)
    {
        // Validar los datos de la solicitud
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',  // Asegúrate de que el usuario exista
            'codigo_qr' => 'required|string|unique:registro_cargas,codigo_qr',  // Validación de QR único
        ]);

        // Crear el nuevo registro de carga
        $registroCarga = new RegistroCarga();
        $registroCarga->usuario_id = $validated['usuario_id'];
        $registroCarga->codigo_qr = $validated['codigo_qr'];
        $registroCarga->estado = 'marcado';  // O cualquier otro valor de estado que consideres adecuado
        $registroCarga->save();

        return response()->json([
            'message' => 'QR marcado como escaneado y registrado',
            'registro' => $registroCarga,
            'timestamp' => now()->toDateTimeString(),  // Agregar la fecha exacta
        ], 201);  // Código de respuesta 201 para creación exitosa
    }

    public function show($id)
    {
        return response()->json(RegistroCarga::with('usuario')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $registroCarga = RegistroCarga::findOrFail($id);
        $registroCarga->update($request->all());
        return response()->json($registroCarga);
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
