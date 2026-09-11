<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use App\Models\Concerns\ResuelveMedios;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Comunicado extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia, ResuelveMedios;

    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'publicado',
        'fecha_publicacion',
        'imagen_url_respaldo',
        'estado_editorial',
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    public function scopePublicados(Builder $query): Builder
    {
        return $query
            ->where('publicado', true)
            ->whereNotNull('fecha_publicacion')
            ->where('fecha_publicacion', '<=', now());
    }

    protected static function booted(): void
    {
        static::creating(function (self $comunicado): void {
            $comunicado->slug ??= Str::slug($comunicado->titulo);
        });

        static::saving(function (self $comunicado): void {
            if ($comunicado->publicado && (blank($comunicado->fecha_publicacion) || blank($comunicado->contenido))) {
                throw ValidationException::withMessages(['publicado' => 'Un comunicado necesita fecha y contenido antes de publicarse.']);
            }
        });
    }

    /**
     * Alias `fecha` para las vistas (mapea a fecha_publicacion).
     */
    public function getFechaAttribute(): ?Carbon
    {
        return $this->fecha_publicacion;
    }

    /**
     * Colección de medios: una sola imagen destacada por comunicado.
     */
    /**
     * Imagen publicada: el archivo subido, si no la URL de respaldo del panel.
     *
     * La conversión («thumb») solo aplica al archivo subido: una dirección
     * externa se sirve tal cual, porque Media Library no generó nada de ella.
     */
    public function imagenUrl(string $conversion = ''): string
    {
        if ($this->archivoSubidoDisponible('imagen')) {
            return $this->getFirstMediaUrl('imagen', $conversion);
        }

        return (string) $this->imagen_url_respaldo;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('imagen')->singleFile();
    }

    /**
     * Miniatura optimizada para listados.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(400)
            ->nonQueued();
    }
}
