<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'ciclo',
        'nombre',
        'creditos',
        'tipo',
        'orden',
    ];

    protected $casts = [
        'ciclo' => 'integer',
        'creditos' => 'integer',
        'orden' => 'integer',
    ];

    /**
     * Tipos de curso para el select del panel.
     */
    public const TIPOS = [
        'General' => 'General',
        'Específico' => 'Específico',
        'Especialidad' => 'Especialidad',
        'Electivo' => 'Electivo',
    ];

    /**
     * Etiqueta legible del ciclo en números romanos (I–X).
     */
    public function getCicloRomanoAttribute(): string
    {
        $romanos = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X'];

        return $romanos[$this->ciclo] ?? (string) $this->ciclo;
    }
}
