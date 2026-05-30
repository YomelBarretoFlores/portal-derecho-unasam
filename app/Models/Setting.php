<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'clave',
        'valor',
    ];

    /**
     * Valor de una clave (o el por defecto si no existe).
     */
    public static function get(string $clave, mixed $default = null): mixed
    {
        return static::query()->where('clave', $clave)->value('valor') ?? $default;
    }

    /**
     * Crea o actualiza una clave.
     */
    public static function set(string $clave, mixed $valor): void
    {
        static::query()->updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
