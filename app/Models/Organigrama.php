<?php

namespace App\Models;

use App\Models\Concerns\ResuelveMedios;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Organigrama extends Model implements HasMedia
{
    use InteractsWithMedia, ResuelveMedios;

    protected $table = 'organigrama';

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen_url_respaldo',
    ];

    /**
     * Imagen del organigrama (una sola).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('imagen')->singleFile();
    }

    /**
     * Devuelve la fila única (singleton), creándola si no existe.
     */
    public static function singleton(): self
    {
        return static::query()->firstOrCreate([]);
    }

    /**
     * Imagen del organigrama: el archivo subido o la URL de respaldo.
     *
     * Antes devolvía null sin más cuando no había archivo subido, y como las
     * cargas están deshabilitadas eso era siempre: la página del organigrama
     * no tenía forma alguna de mostrar un organigrama.
     */
    public function getImagenUrlAttribute(): ?string
    {
        return $this->resolverMedia('imagen', (string) $this->imagen_url_respaldo) ?: null;
    }
}
