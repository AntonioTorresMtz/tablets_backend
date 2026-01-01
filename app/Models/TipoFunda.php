<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoFunda extends Model
{
    public $timestamps = false;
    protected $table = 'cat_tipo_funda';
    use HasFactory;
}
