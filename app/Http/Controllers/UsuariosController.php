<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Usuarios;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Controller
{
    public function index()
    {
        // Consulta los datos de la tabla 'users'
        $usuarios = Usuarios::all();

        // Devuelve los datos como una respuesta JSON
        return response()->json($usuarios);
    }

    public function crearUsuario(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre_usuario' => 'required|string|max:20',
            'nombre_real' => 'required|string|max:50',
            'password' => 'required|string|max:255',
            'permisos' => 'required|integer',
        ]);

        // Crear el usuario con la contraseña encriptada
        $usuario = Usuario::create([
            'nombre_usuario' => $validated['nombre_usuario'],
            'nombre_real' => $validated['nombre_real'],
            'password' => Hash::make($validated['password']), // Encripta la contraseña
            'permisos' => $validated['permisos'],
        ]);

        return response()->json([
            'mensaje' => 'Usuario creado correctamente',
            'usuario' => $usuario,
        ]);
    }

    public function validarUsuario(Request $request)
    {
        $credentials = $request->only('nombre_usuario', 'password');    
        // Intentar autenticar con las credenciales
        if (Auth::attempt($credentials)) {
            // Autenticación exitosa
            return response()->json([
                'mensaje' => 'Validación Correcta',
                'codigo' => 200,                
            ]);
        } else {
            // Autenticación fallida (aunque esto normalmente no se ejecutará si ya se verificó la contraseña)
            return response()->json([
                'mensaje' => 'Datos incorrectos',
                'codigo' => 400
            ]);
        }
    }


}
