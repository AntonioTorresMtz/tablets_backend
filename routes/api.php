<?php

use App\Http\Controllers\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/usuarios', [UsuariosController::class, 'index']);
Route::post('/crearUsuario', [UsuariosController::class, 'crearUsuario']);
Route::post('/validarUsuario', [UsuariosController::class, 'validarUsuario']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
