<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hito extends Model
{
    protected $fillable = [
        'anio',
        'titulo',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'anio' => 'integer',
        'orden' => 'integer',
    ];
}
