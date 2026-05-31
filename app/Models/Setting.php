<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'clave',
        'valor',
    ];

    private const CACHE_KEY = 'settings.map';

    /**
     * Mapa completo clave=>valor, cacheado para evitar consultar la BD
     * (Neon, remota) en cada petición. Se invalida al guardar.
     *
     * @return array<string, string|null>
     */
    public static function map(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => static::query()->pluck('valor', 'clave')->all(),
        );
    }

    /**
     * Valor de una clave (o el por defecto si no existe), desde la caché.
     */
    public static function get(string $clave, mixed $default = null): mixed
    {
        return static::map()[$clave] ?? $default;
    }

    /**
     * Crea o actualiza una clave e invalida la caché.
     */
    public static function set(string $clave, mixed $valor): void
    {
        static::query()->updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget(self::CACHE_KEY);
    }
}
