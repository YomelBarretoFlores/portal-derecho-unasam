<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilIngresoArea extends Model
{
    protected $table = 'perfil_ingreso_areas';

    protected $fillable = [
        'titulo',
        'items',
        'orden',
    ];

    protected $casts = [
        'items' => 'array',
        'orden' => 'integer',
    ];
}
