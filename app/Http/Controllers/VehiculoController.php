<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    /**
     * Devuelve una lista de todos los vehículos
     */
    public function index()
    {
        return response()->json(Vehiculo::with(['usuario', 'tipoVehiculo'])->get());
    }

    /**
     * Almacena un nuevo vehículo
     */
    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'numero_chasis' => 'required|string|unique:vehiculos,numero_chasis',
            'placa' => 'nullable|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'color' => 'nullable|string',
            'anio' => 'nullable|integer',
            'capacidad_tanque_litros' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|string'
        ]);

        $vehiculo = Vehiculo::create($request->all());
        
        return response()->json([
            'message' => 'Vehículo creado exitosamente',
            'data' => $vehiculo
        ], 201);
    }

    /**
     * Muestra un vehículo específico
     */
    public function show($id)
    {
        $vehiculo = Vehiculo::with(['usuario', 'tipoVehiculo'])->findOrFail($id);
        return response()->json($vehiculo);
    }

    /**
     * Actualiza un vehículo específico
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'usuario_id' => 'sometimes|exists:usuarios,id',
            'tipo_vehiculo_id' => 'sometimes|exists:tipos_vehiculo,id',
            'numero_chasis' => 'sometimes|string|unique:vehiculos,numero_chasis,' . $id,
            'placa' => 'nullable|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'color' => 'nullable|string',
            'anio' => 'nullable|integer',
            'capacidad_tanque_litros' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|string'
        ]);

        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->update($request->all());
        
        return response()->json([
            'message' => 'Vehículo actualizado exitosamente',
            'data' => $vehiculo
        ]);
    }

    /**
     * Elimina un vehículo
     */
    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->delete();
        
        return response()->json([
            'message' => 'Vehículo eliminado exitosamente'
        ]);
    }

    /**
     * Devuelve los vehículos de un usuario específico
     */
    public function getByUsuario($usuarioId)
    {
        $vehiculos = Vehiculo::with('tipoVehiculo')
            ->where('usuario_id', $usuarioId)
            ->get();
            
        return response()->json($vehiculos);
    }
}