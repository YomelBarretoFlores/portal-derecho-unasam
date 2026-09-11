<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use App\Models\Concerns\ResuelveMedios;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BlogPost extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia, ResuelveMedios;

    protected $fillable = [
        'tipo',
        'titulo',
        'slug',
        'extracto',
        'contenido',
        'autor',
        'tiempo_lectura',
        'fecha',
        'publicado',
        'imagen_url_respaldo',
        'estado_editorial',
    ];

    protected $casts = [
        'fecha' => 'date',
        'publicado' => 'boolean',
    ];

    public function scopePublicados(Builder $query): Builder
    {
        return $query
            ->where('publicado', true)
            ->whereNotNull('fecha')
            ->whereDate('fecha', '<=', today());
    }

    protected static function booted(): void
    {
        static::creating(function (self $post): void {
            $post->slug ??= Str::slug($post->titulo);
        });

        static::saving(function (self $post): void {
            if ($post->publicado && (blank($post->fecha) || blank($post->contenido))) {
                throw ValidationException::withMessages(['publicado' => 'Una publicación necesita fecha y contenido antes de publicarse.']);
            }
        });
    }

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

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(400)
            ->nonQueued();
    }
}
