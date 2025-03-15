<?php

namespace App\Http\Controllers;

use App\Models\TipoVehiculo;
use Illuminate\Http\Request;

class TipoVehiculoController extends Controller
{
    /**
     * Devuelve una lista de todos los tipos de vehículos
     */
    public function index()
    {
        return response()->json(TipoVehiculo::all());
    }

    /**
     * Almacena un nuevo tipo de vehículo
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'consumo_promedio_litros' => 'required|numeric|min:0'
        ]);

        $tipoVehiculo = TipoVehiculo::create($request->all());
        
        return response()->json([
            'message' => 'Tipo de vehículo creado exitosamente',
            'data' => $tipoVehiculo
        ], 201);
    }

    /**
     * Muestra un tipo de vehículo específico
     */
    public function show($id)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id);
        return response()->json($tipoVehiculo);
    }

    /**
     * Actualiza un tipo de vehículo específico
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'consumo_promedio_litros' => 'sometimes|numeric|min:0'
        ]);

        $tipoVehiculo = TipoVehiculo::findOrFail($id);
        $tipoVehiculo->update($request->all());
        
        return response()->json([
            'message' => 'Tipo de vehículo actualizado exitosamente',
            'data' => $tipoVehiculo
        ]);
    }

    /**
     * Elimina un tipo de vehículo
     */
    public function destroy($id)
    {
        $tipoVehiculo = TipoVehiculo::findOrFail($id);
        $tipoVehiculo->delete();
        
        return response()->json([
            'message' => 'Tipo de vehículo eliminado exitosamente'
        ]);
    }
}