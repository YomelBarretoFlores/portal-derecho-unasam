<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Articulo extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia;

    protected $table = 'articulos';

    protected $fillable = [
        'revista_numero_id',
        'titulo',
        'slug',
        'autores',
        'paginas',
        'categoria',
        'resumen',
        'contenido',
        'fecha',
        'doi',
        'descargas',
        'orden',
        'publicado',
        'estado_editorial',
    ];

    protected $casts = [
        'autores' => 'array',
        'fecha' => 'date',
        'descargas' => 'integer',
        'orden' => 'integer',
        'publicado' => 'boolean',
    ];

    public function numero(): BelongsTo
    {
        return $this->belongsTo(RevistaNumero::class, 'revista_numero_id');
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query
            ->where('publicado', true)
            ->whereNotNull('fecha')
            ->whereDate('fecha', '<=', today())
            ->where(fn (Builder $contenido) => $contenido
                ->where(fn (Builder $texto) => $texto->whereNotNull('contenido')->where('contenido', '!=', ''))
                ->orWhereHas('media', fn (Builder $media) => $media->where('collection_name', 'pdf')))
            ->whereHas('numero', fn (Builder $numero) => $numero->publicados());
    }

    protected static function booted(): void
    {
        static::creating(function (self $articulo): void {
            $articulo->slug ??= Str::slug($articulo->titulo);
        });

        static::saving(function (self $articulo): void {
            if (! $articulo->publicado) {
                return;
            }

            $missing = blank($articulo->revista_numero_id)
                || blank($articulo->fecha)
                || blank($articulo->categoria)
                || blank($articulo->resumen)
                || empty($articulo->autores);

            if ($missing) {
                throw ValidationException::withMessages(['publicado' => 'Asigna un número, autores, categoría, resumen y fecha antes de publicar.']);
            }
        });
    }

    /**
     * Archivo PDF del artículo (uno por registro).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pdf')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
