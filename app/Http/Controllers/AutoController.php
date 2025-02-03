<?php

namespace App\Http\Controllers;

use App\Models\Auto;
use Illuminate\Http\Request;

class AutoController extends Controller
{
    public function index()
    {
        return response()->json(Auto::with('usuario')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_chasis' => 'required|unique:autos,numero_chasis',
            'marca' => 'nullable|string', // Marca es nullable, pero si se pasa debe ser una cadena
            'modelo' => 'nullable|string', // Modelo también es nullable
            'imagen' => 'nullable|string',
            'usuario_id' => 'required|exists:usuarios,id', // Validación de la relación
        ]);
    
        $auto = new Auto();
        $auto->numero_chasis = $request->input('numero_chasis');
        $auto->marca = $request->input('marca');  // Marca
        $auto->modelo = $request->input('modelo');  // Modelo
        $auto->imagen = $request->input('imagen');  // Imagen (ruta opcional)
        $auto->usuario_id = $request->input('usuario_id');
        $auto->save();
    
        return response()->json($auto, 201); // Devolver el auto recién creado
    }

    public function show($id)
    {
        return response()->json(Auto::with('usuario')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $auto = Auto::findOrFail($id);
        $auto->update($request->all());
        return response()->json($auto);
    }

    public function destroy($id)
    {
        Auto::destroy($id);
        return response()->json(['message' => 'Auto eliminado'], 200);
    }
}
