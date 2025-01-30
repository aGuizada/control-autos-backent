<?php

namespace App\Http\Controllers;
use App\Models\Comunidad;
use Illuminate\Http\Request;

class ComunidadController extends Controller
{
    public function index()
    {
        return response()->json(Comunidad::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:comunidades'
        ]);

        $comunidad = Comunidad::create($request->all());
        return response()->json($comunidad, 201);
    }

    public function show($id)
    {
        return response()->json(Comunidad::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $comunidad = Comunidad::findOrFail($id);
        $comunidad->update($request->all());
        return response()->json($comunidad);
    }

    public function destroy($id)
    {
        Comunidad::destroy($id);
        return response()->json(['message' => 'Comunidad eliminada'], 200);
    }
}
