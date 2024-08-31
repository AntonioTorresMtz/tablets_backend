<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'TBL_USUARIOS'; // Nombre de la tabla

    protected $fillable = [
        'nombre_usuario',
        'nombre_real',
        'password',
        'permisos',
    ];
    public $timestamps = false; // Desactiva los timestamps
}
