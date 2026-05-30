<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Articulo extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'articulos';

    protected $fillable = [
        'titulo',
        'slug',
        'autores',
        'paginas',
        'categoria',
        'resumen',
        'fecha',
        'doi',
        'descargas',
        'publicado',
    ];

    protected $casts = [
        'autores' => 'array',
        'fecha' => 'date',
        'descargas' => 'integer',
        'publicado' => 'boolean',
    ];

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
