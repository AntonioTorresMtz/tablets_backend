<?php

namespace App\Http\Controllers;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Illuminate\Http\Request;
use App\Models\Ventas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VentasController extends Controller
{
    public function index()
    {
        // Consulta los datos de la tabla 'users'
        $ventas = Ventas::all();

        // Devuelve los datos como una respuesta JSON
        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $ventas
        ], 200);
    }

    public function obtenerVentas()
    {
        $ventas = DB::table('tbl_venta_productos')
            ->select(
                'PK_venta',
                'fecha_venta',
                'status_venta',
                'total',
            )
            ->whereRaw('DATE(fecha_venta) = CURDATE()')
            ->orderBy('fecha_venta', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $ventas
        ], 200);

    }

    public function cancelarVenta(Request $request)
    {
        try {
            $validated = $request->validate([
                'PK_venta' => 'required|integer',
            ]);

            DB::beginTransaction();

            //Buscar la venta y validar que no esté cancelada
            $venta = Ventas::where('PK_venta', $validated['PK_venta'])
                ->where('status_venta', 1) // 1 = activa
                ->lockForUpdate()
                ->firstOrFail();

            //Regresar stock de productos
            DB::table('tbl_producto as p')
                ->join('tbl_detalle_venta as dv', 'dv.FK_producto', '=', 'p.PK_producto')
                ->join('rel_detalle_venta as rel', 'rel.FK_detalle', '=', 'dv.PK_detalle')
                ->join('tbl_venta_productos as vp', 'vp.PK_venta', '=', 'rel.FK_venta')
                ->where('vp.PK_venta', $validated['PK_venta'])
                ->update([
                    'p.cantidad' => DB::raw('p.cantidad + dv.cantidad_producto')
                ]);

            //Marcar venta como cancelada
            $venta->update([
                'status_venta' => 0, // 0 = cancelada
                'fecha_cancelado' => now()
            ]);

            DB::commit();

            return response()->json([
                'mensaje' => 'Venta cancelada con éxito',
                'success' => true,
                'codigo' => 201,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'La venta no existe o ya fue cancelada'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al cancelar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerVentasFiltrado(Request $request)
    {
        $fecha_inicio = $request['fecha_inicio'];
        $fecha_fin = $request['fecha_fin'];

        $ventas = DB::table('tbl_venta_productos')
            ->select(
                'PK_venta',
                'total',
                'status_venta',
                'fecha_venta'
            )
            ->whereBetween('fecha_venta', [$fecha_inicio, $fecha_fin])
            ->orderBy('fecha_venta', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $ventas
        ], 200);

    }

    public function insertarDetalleVenta(Request $request)
    {
        $datos = $request->all(); // Recibe el array de objetos

        // ✅ Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            // '*.PK_producto' => 'required|integer',
            '*.cantidad' => 'required|integer|min:1',
            '*.garantia' => 'required|integer',
            //  '*.precio_venta' => 'required|integer|min:1',                     
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

    public function imprimirTicket($id_venta = 0)
    {
        if ($id_venta == 0) {
            // Obtener la última venta
            $ultimaVenta = DB::table('tbl_venta_productos')
                ->select('PK_venta', 'fecha_venta', 'total')
                ->orderBy('PK_venta', 'DESC')
                ->limit(1)
                ->first();

            // Verificar si se encontró una venta
            if (!$ultimaVenta) {
                return response()->json(['mensaje' => 'No hay ventas registradas'], 404);
            }
        } else {
            $ultimaVenta = DB::table('tbl_venta_productos')
                ->select('PK_venta', 'fecha_venta', 'total')
                ->where('PK_venta', $id_venta)
                ->first();
        }

        $total = $ultimaVenta->total;
        $idVenta = $ultimaVenta->PK_venta;

        // Obtener los detalles de la última venta
        $detallesVenta = DB::table('tbl_detalle_venta as dv')
            ->select(
                'dv.cantidad_producto',
                'p.nombre_producto',
                'dv.precio_unitario',
                'dv.descuento',
                'dv.importe',
                'p.garantia'
            )
            ->join('tbl_producto as p', 'dv.FK_producto', '=', 'p.PK_producto')
            ->join('rel_detalle_venta as rdv', 'rdv.FK_detalle', '=', 'dv.PK_detalle')
            ->join('tbl_venta_productos as vp', 'rdv.FK_venta', '=', 'vp.PK_venta')
            ->where('vp.PK_venta', $idVenta)
            ->get();

        $connector = new WindowsPrintConnector("POS80");
        // Crear una instancia de la impresora
        $printer = new Printer($connector);
        // Realizar las operaciones de impresión
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        //$printer->setFontSize(2, 2);
        $printer->text("Center Accesories\n");
        $printer->text("Hidalgo #151, Ario de Rosales\n");
        $printer->text($ultimaVenta->fecha_venta . "\n");
        $printer->text("\n");
        $printer->text("\n");
        $printer->text("TICKET DE COMPRA\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("------------------- \n");
        for ($i = 0; $i < count($detallesVenta); $i++) {
            $detalle = $detallesVenta[$i];
            $printer->text("Producto: " . $detalle->nombre_producto . "\n");
            $printer->text("Cantidad: " . $detalle->cantidad_producto . "\n");
            $printer->text("Precio: $" . intval($detalle->precio_unitario) . "\n");
            $printer->text("Descuento: $" . intval($detalle->descuento) . "\n");
            $printer->text("Importe: $" . intval($detalle->importe) . "\n");
            $printer->text("Garantia: " . $detalle->garantia . " dias \n");
            $printer->text("------------------- \n");
        }

        $printer->text("Total: $" . intval($total) . "\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("\n");
        $printer->text("Agradecemos su compra :)\n");
        $printer->feedReverse(3);
        $printer->cut();
        $printer->close();


        // Retornar la información en formato JSON
        return response()->json([
            'success' => true,
            'message' => 'Ticket impreso',
            'codigo' => 200,
        ], 201);

    }

    public function obtenerDetalleVentas($id)
    {
        $detalles = DB::table('tbl_detalle_venta as dv')
            ->join('tbl_producto as p', 'p.PK_producto', '=', 'dv.FK_producto')
            ->join('rel_detalle_venta as rdv', 'rdv.FK_detalle', '=', 'dv.PK_detalle')
            ->join('tbl_venta_productos as vp', 'vp.PK_venta', '=', 'rdv.FK_venta')
            ->where('vp.PK_venta', $id)
            ->select(
                'dv.PK_detalle',
                'p.nombre_producto',
                'dv.cantidad_producto',
                'dv.precio_unitario',
                'dv.descuento',
                'dv.importe',
                'vp.fecha_venta',
                'vp.PK_venta',
                'vp.total'
            )
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $detalles
        ], 200);
    }
}
