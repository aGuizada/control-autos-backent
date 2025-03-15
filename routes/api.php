<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\SurtidorController;
use App\Http\Controllers\RegistroCargaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TipoVehiculoController;

// Ruta para login
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas con `auth:sanctum`
Route::middleware('auth:sanctum')->group(function () {
    // Rutas originales (mantener compatibilidad)
    Route::apiResource('roles', RolController::class);
    Route::apiResource('comunidades', ComunidadController::class);
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('registros-carga', RegistroCargaController::class);
    
    // Rutas adicionales para RegistroCarga (compatibles con las que ya tenías)
    Route::get('registros-carga/check-qr-status/{usuarioId}', [RegistroCargaController::class, 'checkQRStatus']);
    Route::post('registros-carga/marcar-qr', [RegistroCargaController::class, 'marcarQR']);
    Route::post('registros-carga/{id}/habilitar-qr', [RegistroCargaController::class, 'habilitarQR']);
    
    // Nuevas rutas para Vehículos
    Route::apiResource('vehiculos', VehiculoController::class);
    Route::get('usuarios/{id}/vehiculos', [UsuarioController::class, 'getVehiculos']);
    Route::post('usuarios/{id}/vehiculos', [UsuarioController::class, 'addVehiculo']);
    
    // Nuevas rutas para Surtidores
    Route::apiResource('surtidores', SurtidorController::class);
    Route::post('surtidores/{id}/recargar', [SurtidorController::class, 'recargar']);
    
    // Rutas para Tipos de Vehículo
    Route::apiResource('tipos-vehiculo', TipoVehiculoController::class);
    
    // Obtener datos del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Obtener el rol del usuario autenticado
    Route::get('/user/role', function (Request $request) {
        return response()->json([
            'role' => $request->user()->rol->nombre, 
        ]);
    });
    
    // Obtener el perfil del usuario autenticado
    Route::get('/user-profile', function (Request $request) {
        return response()->json($request->user()->perfil());  
    });
});