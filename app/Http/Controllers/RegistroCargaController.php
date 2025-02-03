<?php

namespace App\Http\Controllers;

use App\Models\RegistroCarga;
use Illuminate\Http\Request;

class RegistroCargaController extends Controller
{
    public function index()
    {
        return response()->json(RegistroCarga::with('usuario')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
        ]);

        $registroCarga = RegistroCarga::create([
            'usuario_id' => $request->usuario_id,
            'fecha_carga' => now(),  // Agregamos la fecha actual automáticamente
        ]);

        return response()->json($registroCarga, 201);
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
