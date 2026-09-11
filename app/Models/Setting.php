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
     * Crea o actualiza una clave. La caché la invalidan los hooks de abajo.
     */
    public static function set(string $clave, mixed $valor): void
    {
        static::query()->updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }

    public static function olvidarCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * La invalidación vive en los eventos del modelo, no en set(), para que
     * ninguna vía de escritura pueda saltársela: un seeder, un updateOrCreate
     * directo o un futuro recurso de Filament invalidan igual. Al ser una caché
     * rememberForever, una escritura sin invalidar dejaría el pie de página, la
     * navegación y el SEO congelados de forma indefinida.
     */
    protected static function booted(): void
    {
        static::saved(static fn () => static::olvidarCache());
        static::deleted(static fn () => static::olvidarCache());
    }
}
