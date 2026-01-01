<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posiciones extends Model
{
    public $timestamps = false;
    protected $table = 'tbl_posiciones';
    use HasFactory;
}
