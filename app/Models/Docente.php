<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Docente extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'area',
        'grado',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Iniciales para el avatar: 1ª letra del nombre + 1ª del último apellido,
     * descartando el título (Dr./Dra./Mg.).
     */
    public function getInicialesAttribute(): string
    {
        $sinTitulo = Str::of($this->name ?? '')
            ->replaceMatches('/^(Dr\.|Dra\.|Mg\.)\s+/', '')
            ->trim();

        $palabras = $sinTitulo->explode(' ');
        $iniciales = Str::substr($palabras->first(), 0, 1) . Str::substr($palabras->last(), 0, 1);

        return Str::upper($iniciales);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('foto')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(240)
            ->height(240)
            ->nonQueued();
    }
}
