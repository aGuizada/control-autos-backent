<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\RegistroCargaController;
use App\Http\Controllers\AuthController;

// Rutas para Roles
Route::apiResource('roles', RolController::class);

// Rutas para Comunidades
Route::apiResource('comunidades', ComunidadController::class);

// Rutas para Usuarios
Route::apiResource('usuarios', UsuarioController::class);

// Rutas para Autos
Route::apiResource('autos', AutoController::class);

// Rutas para Registros de Carga
Route::apiResource('registros-carga', RegistroCargaController::class);

// Rutas adicionales para Registros de Carga
Route::post('registros-carga', [RegistroCargaController::class, 'store']);
Route::get('registros-carga/check-qr-status/{usuarioId}', [RegistroCargaController::class, 'checkQRStatus']);
Route::put('registros-carga/{usuarioId}/marcar-qr', [RegistroCargaController::class, 'marcarQR'])
     ->name('registros-carga.marcarQR');
Route::post('registros-carga/marcar-qr', [RegistroCargaController::class, 'marcarQR']);

// Ruta para login
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas con `auth:sanctum`
Route::middleware('auth:sanctum')->group(function () {
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

    // Obtener usuario autenticado
    Route::get('/usuario-autenticado', [AuthController::class, 'getUsuarioAutenticado']);
});
