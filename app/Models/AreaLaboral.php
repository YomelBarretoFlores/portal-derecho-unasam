<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaLaboral extends Model
{
    protected $table = 'areas_laborales';

    protected $fillable = [
        'titulo',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];
}
