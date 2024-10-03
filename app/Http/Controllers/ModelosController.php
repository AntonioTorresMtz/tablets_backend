<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;

class ModelosController extends Controller
{

    public function index()
    {
        //
    }


    public function crearModelo(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'nombre_modelo' => 'required|string|max:50',
            'FK_marca' => 'required|integer',
            'pulgadas' => 'nullable|numeric'
        ]);

        try {
            // Crear nueva instancia del modelo y asignar valores
            $modelo = new Modelo;
            $modelo->nombre_modelo = $validated['nombre_modelo'];
            $modelo->FK_marca = $validated['FK_marca'];
            $modelo->pulgadas = $validated['pulgadas'];

            // Guardar en la base de datos
            $modelo->save();

            // Retornar una respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Modelo creado exitosamente',
                'codigo' => 201,
                'data' => $modelo
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

   
    public function show(Modelo $modelo)
    {
        //
    }

    
    public function edit(Modelo $modelo)
    {
        //
    }


    public function update(Request $request, Modelo $modelo)
    {
        //
    }

    
    public function destroy(Modelo $modelo)
    {
        //
    }
}
