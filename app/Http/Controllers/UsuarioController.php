<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json(Usuario::with(['rol', 'comunidad'])->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|string|min:6',
            'telefono' => 'nullable|string',
            'rol_id' => 'required|exists:roles,id',
            'comunidad_id' => 'required|exists:comunidades,id'
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol_id' => $request->rol_id,
            'comunidad_id' => $request->comunidad_id
        ]);

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        return response()->json(Usuario::with(['rol', 'comunidad'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update($request->except('password'));
        return response()->json($usuario);
    }

    public function destroy($id)
    {
        Usuario::destroy($id);
        return response()->json(['message' => 'Usuario eliminado'], 200);
    }
}
