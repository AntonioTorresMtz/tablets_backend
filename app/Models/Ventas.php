<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    protected $table = 'TBL_VENTA_PRODUCTOS';
    public $timestamps = false; 
    protected $primaryKey = 'PK_venta';
         protected $fillable = [
        //'usuario_cancelar',
        'fecha_cancelado',
        'status_venta', // 👈 agrega este
    ];
}
