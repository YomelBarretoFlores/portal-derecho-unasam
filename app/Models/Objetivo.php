<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model
{
    protected $fillable = [
        'plan',
        'vigente',
        'texto',
        'orden',
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'orden' => 'integer',
    ];
}
