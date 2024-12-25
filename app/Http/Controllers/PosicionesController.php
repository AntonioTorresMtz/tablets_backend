<?php

namespace App\Http\Controllers;

use App\Models\Posiciones;
use Illuminate\Http\Request;

class PosicionesController extends Controller
{
    public function index()
    {
        //
    }


    public function crearPosicion(Request $request)
    {
        $validated = $request->validate([
            'muro' => 'required|integer',
            'clave' => 'required|string|max:5',
            'cantidad' => 'required|integer'
        ]);

        try {
            // Crear nueva instancia del modelo y asignar valores
            $posicion = new Posiciones;

            $listaPosiciones = [];
            for ($i = 1; $i <= $validated['cantidad']; $i++) {
                $listaPosiciones[] = [
                    'muro' => 1,
                    'clave_posicion' => $request['clave'] . $i
                ];
            }

            Posiciones::insert($listaPosiciones);

            // Retornar una respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Posiciones creadas con exito',
                'codigo' => 201,
                'data' => $posicion
            ], 201); // Código HTTP 201 para creación exitosa

        } catch (\Exception $e) {
            // Manejar cualquier error y retornar una respuesta
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la marca',
                'error' => $e->getMessage()
            ], 500); // Código HTTP 500 para error interno del servidor
        }

    }


    public function store(Request $request)
    {
        //
    }


    public function show(Posiciones $posiciones)
    {
        //
    }


    public function edit(Posiciones $posiciones)
    {
        //
    }


    public function update(Request $request, Posiciones $posiciones)
    {
        //
    }


    public function destroy(Posiciones $posiciones)
    {
        //
    }
}
