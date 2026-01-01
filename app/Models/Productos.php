<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $table = 'TBL_PRODUCTO';
    public $timestamps = false;
     protected $primaryKey = 'PK_producto';
    protected $fillable = [
        'nombre_producto',
        'clave',
        'codigo_barras',
        'precio_compra',
        'precio_venta',
        'garantia',
    ];
}
