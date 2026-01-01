<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{

    public function handle(Request $request, Closure $next)
    {
        // 1. Si es una petición OPTIONS (Preflight), respondemos directamente
        if ($request->isMethod('OPTIONS')) {
            return response()->json('OK', 200)
                ->header("Access-Control-Allow-Origin", "http://localhost")
                ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, PATCH, OPTIONS")
                ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization, Accept")
                ->header("Access-Control-Allow-Credentials", "true");
        }

        // 2. Para el resto de peticiones, añadimos las cabeceras a la respuesta final
        return $next($request)
            ->header("Access-Control-Allow-Origin", "http://localhost")
            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, PATCH, OPTIONS")
            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization, Accept");
    }
}
