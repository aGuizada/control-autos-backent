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
            'numero_chasis' => 'required|unique:autos',
            'imagen' => 'nullable|string',
            'usuario_id' => 'required|exists:usuarios,id'
        ]);

        $auto = Auto::create($request->all());
        return response()->json($auto, 201);
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
