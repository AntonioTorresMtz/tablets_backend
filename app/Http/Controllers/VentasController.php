<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VentasController extends Controller
{
    public function insertarDetalleVenta(Request $request)
    {
        $datos = $request->all(); // Recibe el array de objetos

        // ✅ Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            '*.id' => 'required|integer',
            '*.cantidad' => 'required|integer|min:1',
            '*.precio' => 'required|integer|min:0',
            '*.descuento' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // ✅ Insertar los datos en la base de datos
        try {
            DB::beginTransaction();

            // ✅ 1. Insertar en TBL_VENTA_PRODUCTOS
            $total = 0;
            foreach ($request->all() as $detalle) {
                $total += ($detalle['cantidad'] * $detalle['precio']) - $detalle['descuento'];
            }

            $idVenta = DB::table('TBL_VENTA_PRODUCTOS')->insertGetId([
                'usuario' => 1, // Ajusta este valor según corresponda
                'total' => $total,
            ]);

            // ✅ 2. Insertar en TBL_DETALLE_VENTA y REL_DETALLE_VENTA
            foreach ($request->all() as $detalle) {
                $idDetalle = DB::table('TBL_DETALLE_VENTA')->insertGetId([
                    'FK_producto' => $detalle['id'],
                    'cantidad_producto' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio'],
                    'descuento' => $detalle['descuento'],
                    'importe' => ($detalle['cantidad'] * $detalle['precio']) - $detalle['descuento']
                ]);

                $cantidad = $detalle['cantidad'];
                DB::table('TBL_PRODUCTO')
                    ->where('PK_producto', $detalle['id']) // Filtrar registros
                    ->update([
                        'cantidad' => DB::raw("cantidad - $cantidad")
                    ]);


                // ✅ 3. Insertar en REL_DETALLE_VENTA
                DB::table('REL_DETALLE_VENTA')->insert([
                    'FK_detalle' => $idDetalle,
                    'FK_venta' => $idVenta,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada con exito',
                'codigo' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al insertar detalle de venta: ' . $e->getMessage()], 500);
        }
    }
}
