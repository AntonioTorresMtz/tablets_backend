<?php

namespace App\Http\Controllers;

use App\Models\Marcas;
use Illuminate\Http\Request;

class MarcasController extends Controller
{
    public function index()
    {
        $marcas = Marcas::all();
        //$marcas = Marcas::select(['PK_marca', 'nombre_marca'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $marcas,
            'codigo' => 200,
            'error' => null,
            'mensaje' => 'Consulta realizada con exito'
        ]);
    }


    public function crearMarca(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'nombre_marca' => 'required|string|max:50'
        ]);

        try {
            // Crear nueva instancia del modelo y asignar valores
            $marca = new Marcas;
            $marca->nombre_marca = $validated['nombre_marca'];

            // Guardar en la base de datos
            $marca->save();

            // Retornar una respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Marca creada exitosamente',
                'codigo' => 201,
                'data' => $marca
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


    public function show(Marcas $marcas)
    {
        //
    }

    public function edit(Marcas $marcas)
    {
        //
    }

    public function update(Request $request, Marcas $marcas)
    {
        //
    }


    public function destroy(Marcas $marcas)
    {
        //
    }
}
