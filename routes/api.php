<?php

use App\Http\Controllers\ModelosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\MarcasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/usuarios', [UsuariosController::class, 'index']);
Route::post('/crearUsuario', [UsuariosController::class, 'crearUsuario']);
Route::post('/validarUsuario', [UsuariosController::class, 'validarUsuario']);
Route::post('/marcas/crearMarca', [MarcasController::class, 'crearMarca']);
Route::post('/modelos/crearModelo', [ModelosController::class, 'crearModelo']);
Route::get('/marcas', [MarcasController::class, 'index']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
