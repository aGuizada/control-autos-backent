<?php

namespace App\Http\Controllers;

use App\Models\RegistroCarga;
use Illuminate\Http\Request;

class RegistroCargaController extends Controller
{
    public function index()
    {
        return response()->json(RegistroCarga::with('auto')->get());
    }

    public function store(Request $request)
    {
        // Validación (opcional)
        $request->validate([
            'auto_id' => 'required|exists:autos,id',
            'fecha_carga' => 'required|date'
        ]);
    
        // Crear un nuevo registro de carga
        $registroCarga = RegistroCarga::create($request->all());
    
        return response()->json($registroCarga, 201);
    }
    public function show($id)
    {
        return response()->json(RegistroCarga::with('auto')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $registroCarga = RegistroCarga::findOrFail($id);
        $registroCarga->update($request->all());
        return response()->json($registroCarga);
    }

    public function destroy($id)
    {
        RegistroCarga::destroy($id);
        return response()->json(['message' => 'Registro de carga eliminado'], 200);
    }
}
