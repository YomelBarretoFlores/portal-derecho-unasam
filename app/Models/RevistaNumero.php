<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RevistaNumero extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia;

    protected $table = 'revista_numeros';

    protected $fillable = [
        'revista_id', 'volumen', 'numero', 'slug', 'titulo', 'subtitulo',
        'descripcion', 'fecha_publicacion', 'orden', 'publicado',
        'es_actual',
        'estado_editorial',
        'portada_url_respaldo', 'pdf_url_respaldo',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'orden' => 'integer',
        'publicado' => 'boolean',
        'es_actual' => 'boolean',
    ];

    /**
     * Segmentos que ya ocupan una ruta estática bajo /revista. Un número con uno
     * de estos slugs quedaría inalcanzable, porque la ruta fija se declara antes
     * que /revista/{numero:slug}. Se derivan del router para que no se
     * desincronicen si se añaden páginas nuevas al micrositio.
     *
     * @return array<int, string>
     */
    public static function slugsReservados(): array
    {
        return collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route): string => $route->uri())
            ->filter(fn (string $uri): bool => str_starts_with($uri, 'revista/') && ! str_contains($uri, '{'))
            ->map(fn (string $uri): string => explode('/', $uri)[1])
            ->unique()->values()->all();
    }

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class)->orderBy('orden')->orderBy('fecha');
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query
            ->where('publicado', true)
            ->whereNotNull('fecha_publicacion')
            ->whereDate('fecha_publicacion', '<=', today())
            ->whereHas('revista', fn (Builder $revista) => $revista->publica());
    }

    /**
     * Portada: el archivo subido si existe, si no la URL pública de respaldo.
     * Sin cargas habilitadas el respaldo manda, porque Media Library no puede
     * resolver nada en ese entorno (mismo criterio que RevistaDocumento).
     */
    public function getPortadaUrlAttribute(): string
    {
        return $this->resolverMedia('portada', (string) $this->portada_url_respaldo);
    }

    /** PDF del número completo: archivo subido o URL pública de respaldo. */
    public function getPdfUrlAttribute(): string
    {
        return $this->resolverMedia('numero_pdf', (string) $this->pdf_url_respaldo);
    }

    private function resolverMedia(string $coleccion, string $respaldo): string
    {
        if (! config('media.uploads_enabled') && filled($respaldo)) {
            return $respaldo;
        }

        return $this->getFirstMediaUrl($coleccion) ?: $respaldo;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portada')->singleFile();
        $this->addMediaCollection('numero_pdf')->singleFile()->acceptsMimeTypes(['application/pdf']);
    }

    protected static function booted(): void
    {
        static::creating(function (self $numero): void {
            $numero->slug ??= Str::slug("volumen-{$numero->volumen}-numero-{$numero->numero}");
        });

        static::saving(function (self $numero): void {
            if ($numero->publicado && (blank($numero->volumen) || blank($numero->numero) || blank($numero->titulo) || blank($numero->fecha_publicacion))) {
                throw ValidationException::withMessages(['publicado' => 'Completa volumen, número, título y fecha antes de publicar.']);
            }
        });

        static::saved(function (self $numero): void {
            if ($numero->es_actual) {
                static::query()->where('revista_id', $numero->revista_id)
                    ->whereKeyNot($numero->getKey())->where('es_actual', true)
                    ->update(['es_actual' => false]);
            }
        });
    }
}
