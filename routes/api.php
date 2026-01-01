<?php

use App\Http\Controllers\ModelosController;
use App\Http\Controllers\TipoFundaController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PosicionesController;
use App\Http\Controllers\MarcasController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\VentasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/usuarios', [UsuariosController::class, 'index']);
Route::get('/productos', [ProductosController::class, 'index']);
Route::get('/ventas', [VentasController::class, 'index']);
Route::get('/ventas/obtenerVentas', [VentasController::class, 'obtenerVentas']);
Route::post('/ventas/obtenerVentasFiltrado', [VentasController::class, 'obtenerVentasFiltrado']);
Route::get('/ventas/obtenerDetalleVentas/{id}', [VentasController::class, 'obtenerDetalleVentas']);
Route::patch('/ventas/cancelarVenta', [VentasController::class, 'cancelarVenta']);
Route::get('/ventas/imprimirTicket/{id_venta}', [VentasController::class, 'imprimirTicket']);
//Route::get('/ventas/imprimirTicket', [VentasController::class, 'imprimirTicket']);
Route::post('/productos/actualizarCantidad', [ProductosController::class, 'actualizarCantidad']);
Route::post('/ventas/insertarDetalleVenta', [VentasController::class, 'insertarDetalleVenta']);
Route::post('/productos/BuscarClave', [ProductosController::class, 'buscarProducto']);
Route::post('/crearUsuario', [UsuariosController::class, 'crearUsuario']);
Route::post('/validarUsuario', [UsuariosController::class, 'validarUsuario']);
Route::post('/marcas/crearMarca', [MarcasController::class, 'crearMarca']);
Route::post('/modelos/crearModelo', [ModelosController::class, 'crearModelo']);
Route::post('/fundasTipos/crearTipoFunda', [TipoFundaController::class, 'crearTipoFunda']);
Route::post('/posiciones/crearPosicion', [PosicionesController::class, 'crearPosicion']);
Route::get('/marcas', [MarcasController::class, 'index']);
Route::post('/productos/agregarProducto', [ProductosController::class, 'agregarProducto']);
Route::patch('/productos/{id}', [ProductosController::class, 'editarProducto']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
