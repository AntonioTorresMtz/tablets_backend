<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use Illuminate\Support\Facades\DB;

class ProductosController extends Controller
{
    public function index()
    {
        // Consulta los datos de la tabla 'users'
        $productos = Productos::all();

        // Devuelve los datos como una respuesta JSON
        return response()->json($productos);
    }

    public function buscarProducto(Request $request)
    {
        $validate = $request->validate([
            'clave' => 'required|string|max:20'
        ]);

        try {
          $resultado = DB::select('CALL SP_BUSCAR_PRODUCTO(:param1)', [
                'param1' => $request['clave'],
            ]);

            // Retornar una respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Consulta exitosa',
                'codigo' => 200,
                'data' => $resultado
            ], 200); // Código HTTP 201 para consulta exitosa

        } catch (\Exception $e) {
            // Manejar cualquier error y retornar una respuesta
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar',
                'error' => $e->getMessage()
            ], 500); // Código HTTP 500 para error interno del servidor
        }
    }
}
