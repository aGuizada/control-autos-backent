<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        // Mantener la misma estructura que tenías antes pero ahora incluir vehículos también
        $usuarios = Usuario::with(['rol', 'comunidad', 'vehiculos.tipoVehiculo'])->get();
        
        return response()->json($usuarios);
    }

    public function store(Request $request)
    {
        // Validar primero los datos de usuario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|string|min:6',
            'telefono' => 'nullable|string',
            'rol_id' => 'required|exists:roles,id',
            'comunidad_id' => 'required|exists:comunidades,id',
            
            // Datos del vehículo (ahora opcional, ya que se guardarán separados)
            'numero_chasis' => 'required|string|unique:vehiculos,numero_chasis',
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'imagen' => 'nullable|string'
        ]);

        // Crear el usuario
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol_id' => $request->rol_id,
            'comunidad_id' => $request->comunidad_id
        ]);

        // Crear el vehículo asociado al usuario
        $vehiculo = Vehiculo::create([
            'usuario_id' => $usuario->id,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'numero_chasis' => $request->numero_chasis,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'imagen' => $request->imagen
        ]);

        // Cargar la relación de vehículo
        $usuario->load('vehiculos');

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        // Incluir vehículos en la respuesta
        return response()->json(Usuario::with(['rol', 'comunidad', 'vehiculos.tipoVehiculo'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:usuarios,email,' . $id,
            'telefono' => 'nullable|string',
            'rol_id' => 'sometimes|exists:roles,id',
            'comunidad_id' => 'sometimes|exists:comunidades,id',
        ]);

        // Actualizar el usuario
        $usuario->update($request->only([
            'nombre', 'email', 'telefono', 'rol_id', 'comunidad_id'
        ]));

        // Si también se están enviando datos del vehículo, actualizarlos
        if ($request->has('numero_chasis') || $request->has('marca') || $request->has('modelo')) {
            // Encontrar el vehículo principal del usuario (asumiendo que es el primero)
            $vehiculo = $usuario->vehiculos()->first();
            
            if ($vehiculo) {
                $vehiculoData = [];
                
                if ($request->has('numero_chasis')) {
                    $request->validate([
                        'numero_chasis' => 'string|unique:vehiculos,numero_chasis,' . $vehiculo->id
                    ]);
                    $vehiculoData['numero_chasis'] = $request->numero_chasis;
                }
                
                if ($request->has('marca')) {
                    $vehiculoData['marca'] = $request->marca;
                }
                
                if ($request->has('modelo')) {
                    $vehiculoData['modelo'] = $request->modelo;
                }
                
                if ($request->has('imagen')) {
                    $vehiculoData['imagen'] = $request->imagen;
                }
                
                if ($request->has('tipo_vehiculo_id')) {
                    $request->validate([
                        'tipo_vehiculo_id' => 'exists:tipos_vehiculo,id'
                    ]);
                    $vehiculoData['tipo_vehiculo_id'] = $request->tipo_vehiculo_id;
                }
                
                $vehiculo->update($vehiculoData);
            }
        }

        // Volver a cargar el usuario con sus relaciones
        $usuario->load(['rol', 'comunidad', 'vehiculos.tipoVehiculo']);

        return response()->json($usuario);
    }

    public function destroy($id)
    {
        // Al eliminar el usuario, los vehículos asociados se eliminarán automáticamente
        // gracias a la relación cascade definida en la migración
        Usuario::destroy($id);
        return response()->json(['message' => 'Usuario eliminado'], 200);
    }
    
    /**
     * Obtener vehículos de un usuario específico
     */
    public function getVehiculos($id)
    {
        $usuario = Usuario::findOrFail($id);
        return response()->json($usuario->vehiculos()->with('tipoVehiculo')->get());
    }
    
    /**
     * Añadir un nuevo vehículo a un usuario existente
     */
    public function addVehiculo(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $request->validate([
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'numero_chasis' => 'required|string|unique:vehiculos,numero_chasis',
            'placa' => 'nullable|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'color' => 'nullable|string',
            'anio' => 'nullable|integer',
            'capacidad_tanque_litros' => 'nullable|numeric',
            'imagen' => 'nullable|string'
        ]);
        
        $vehiculo = new Vehiculo($request->all());
        $vehiculo->usuario_id = $usuario->id;
        $vehiculo->save();
        
        return response()->json([
            'message' => 'Vehículo añadido exitosamente',
            'vehiculo' => $vehiculo
        ], 201);
    }
}