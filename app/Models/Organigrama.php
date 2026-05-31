<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Organigrama extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'organigrama';

    protected $fillable = [
        'titulo',
        'descripcion',
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
     * URL de la imagen subida (o null).
     */
    public function getImagenUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('imagen') ?: null;
    }
}
