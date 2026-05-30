<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estadistica extends Model
{
    protected $table = 'estadisticas';

    protected $fillable = [
        'tipo',
        'anio',
        'total',
    ];

    protected $casts = [
        'anio' => 'integer',
        'total' => 'integer',
    ];

    /**
     * Tipos de indicador con su etiqueta legible.
     */
    public const TIPOS = [
        'matriculados' => 'Estudiantes matriculados',
        'egresados' => 'Egresados',
        'graduados' => 'Graduados (Bachiller)',
        'titulados' => 'Titulados (Abogado)',
    ];
}
