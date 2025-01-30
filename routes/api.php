<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\RegistroCargaController;

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


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
