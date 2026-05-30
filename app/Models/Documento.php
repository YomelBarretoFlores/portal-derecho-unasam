<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Documento extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'documentos_normativos';

    protected $fillable = [
        'titulo',
        'categoria',
        'fecha',
        'url',
        'orden',
    ];

    protected $casts = [
        'fecha' => 'date',
        'orden' => 'integer',
    ];

    /**
     * Archivo PDF del documento (uno por registro).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('archivo')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    /**
     * URL de descarga: el PDF subido si existe, si no el enlace externo.
     */
    public function getEnlaceAttribute(): ?string
    {
        $media = $this->getFirstMedia('archivo');

        return $media?->getUrl() ?: $this->url;
    }
}
