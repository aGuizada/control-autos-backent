<?php

namespace App\Http\Controllers;

use App\Models\Surtidor;
use Illuminate\Http\Request;

class SurtidorController extends Controller
{
    /**
     * Devuelve una lista de todos los surtidores
     */
    public function index()
    {
        return response()->json(Surtidor::all());
    }

    /**
     * Almacena un nuevo surtidor
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string',
            'capacidad_total_litros' => 'required|numeric|min:0',
            'combustible_disponible_litros' => 'required|numeric|min:0|lte:capacidad_total_litros',
            'tipo_combustible' => 'required|string',
            'activo' => 'nullable|boolean'
        ]);

        $surtidor = Surtidor::create($request->all());
        
        return response()->json([
            'message' => 'Surtidor creado exitosamente',
            'data' => $surtidor
        ], 201);
    }

    /**
     * Muestra un surtidor específico
     */
    public function show($id)
    {
        $surtidor = Surtidor::findOrFail($id);
        return response()->json($surtidor);
    }

    /**
     * Actualiza un surtidor específico
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'ubicacion' => 'nullable|string',
            'capacidad_total_litros' => 'sometimes|numeric|min:0',
            'combustible_disponible_litros' => 'sometimes|numeric|min:0|lte:capacidad_total_litros',
            'tipo_combustible' => 'sometimes|string',
            'activo' => 'nullable|boolean'
        ]);

        $surtidor = Surtidor::findOrFail($id);
        $surtidor->update($request->all());
        
        return response()->json([
            'message' => 'Surtidor actualizado exitosamente',
            'data' => $surtidor
        ]);
    }

    /**
     * Elimina un surtidor
     */
    public function destroy($id)
    {
        $surtidor = Surtidor::findOrFail($id);
        $surtidor->delete();
        
        return response()->json([
            'message' => 'Surtidor eliminado exitosamente'
        ]);
    }

    /**
     * Recarga el combustible de un surtidor
     */
    public function recargar(Request $request, $id)
    {
        $request->validate([
            'cantidad_litros' => 'required|numeric|min:0'
        ]);

        $surtidor = Surtidor::findOrFail($id);
        $cantidadLitros = $request->input('cantidad_litros');
        
        if ($surtidor->recargarCombustible($cantidadLitros)) {
            return response()->json([
                'message' => 'Surtidor recargado exitosamente',
                'data' => $surtidor
            ]);
        } else {
            return response()->json([
                'message' => 'No se pudo recargar el surtidor. Capacidad excedida.',
                'data' => $surtidor
            ], 400);
        }
    }
}