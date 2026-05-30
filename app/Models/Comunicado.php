<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Comunicado extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'publicado',
        'fecha_publicacion',
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    /**
     * Alias `fecha` para las vistas (mapea a fecha_publicacion).
     */
    public function getFechaAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->fecha_publicacion;
    }

    /**
     * Colección de medios: una sola imagen destacada por comunicado.
     */
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
