<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    protected $fillable = [
        'grupo',
        'plan',
        'vigente',
        'nombre',
        'texto',
        'orden',
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Prefijo del código de competencia según el grupo (CG / CE).
     */
    public function getPrefijoAttribute(): string
    {
        return str_starts_with($this->grupo, 'Espec') ? 'CE' : 'CG';
    }
}
