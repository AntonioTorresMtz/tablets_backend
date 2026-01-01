<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductosController extends Controller
{
    public function index()
    {
        // Consulta los datos de la tabla 'users'
        $productos = Productos::all();

        // Devuelve los datos como una respuesta JSON
        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $productos
        ], 200);
    }

    public function agregarProducto(Request $request)
    {
        $validated = $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'clave' => 'required|string|max:255|unique:tbl_producto',
            'precio_compra' => 'required|integer',
            'precio_venta' => 'required|integer',
            'garantia' => 'required|integer',
        ]);
        $producto = Productos::create([
            'nombre_producto' => $validated['nombre_producto'],
            'clave' => $validated['clave'],
            'codigo_barras' => $request['codigo_barras'],
            'precio_compra' => $validated['precio_compra'],
            'precio_venta' => $validated['precio_venta'],
            'garantia' => $validated['garantia'],
        ]);
        return response()->json([
            'mensaje' => 'Producto creado correctamente',
            'success' => true,
            'codigo' => 201,
            'usuario' => $producto,
        ], 201);
    }

    public function editarProducto($id, Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre_producto' => 'required|string|max:150',
                'clave' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tbl_producto', 'clave')->ignore($id, 'PK_producto'),
                ],
                'precio_compra' => 'required|integer',
                'precio_venta' => 'required|integer',
                'garantia' => 'required|integer',
            ]);

            //Buscar el Producto por ID
            $producto = Productos::findOrFail($id);
            //Actulizar producto
            $producto->update([
                'nombre_producto' => $validated['nombre_producto'],
                'clave' => $validated['clave'],
                'codigo_barras' => $request['codigo_barras'],
                'precio_compra' => $validated['precio_compra'],
                'precio_venta' => $validated['precio_venta'],
                'garantia' => $validated['garantia'],
            ]);
            return response()->json([
                'mensaje' => 'Producto editado correctamente',
                'success' => true,
                'codigo' => 201,
                'usuario' => $producto,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el usuario', 'error' => $e->getMessage()], 500);
        }
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

    public function actualizarCantidad(Request $request)
    {
        $validate = $request->validate([
            'id_producto' => 'required|integer',
            'cantidad' => 'required|integer'
        ]);

        $cantidad = $validate['cantidad'];
        try {
            $resultado = DB::table('tbl_producto')
                ->where('PK_producto', $validate['id_producto']) // Filtrar registros
                ->update([
                    'cantidad' => DB::raw("cantidad + $cantidad")
                ]);

            // Retornar una respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Datos actualizados con exito',
                'codigo' => 201,
                'data' => $resultado
            ], 201); // Código HTTP 201 para consulta exitosa

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
