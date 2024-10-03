<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    public $timestamps = false;
    protected $table = 'TBL_MODELO';
    use HasFactory;
}
