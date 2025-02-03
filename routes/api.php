]<?php
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

// Ruta para login
Route::post('login', [AuthController::class, 'login']);
// routes/api.php
Route::put('registros-carga/{usuarioId}/marcar-qr', [RegistroCargaController::class, 'marcarQR'])
     ->name('marcar-qr');

     Route::post('registros-carga/marcar-qr', [RegistroCargaController::class, 'marcarQR']);


// Ruta para obtener los datos del usuario autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Ruta para obtener el rol del usuario autenticado
Route::middleware('auth:sanctum')->get('/user/role', function (Request $request) {
    // Devuelve el rol del usuario autenticado
    return response()->json([
        'role' => $request->user()->rol->nombre, // O cualquier campo del modelo Rol que necesites
    ]);
});
// Ruta para obtener los datos del perfil del usuario autenticado
Route::middleware('auth:sanctum')->get('/user-profile', function (Request $request) {
    return response()->json($request->user()->perfil());  // Devuelve el perfil del usuario
});
Route::middleware('auth:sanctum')->get('/usuario-autenticado', [AuthController::class, 'getUsuarioAutenticado']);



