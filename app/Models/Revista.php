<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Revista extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia;

    public const RESOLUTION_PUBLIC_PATH = 'docs/revista/resolucion-creacion-v1.pdf';

    protected $fillable = [
        'nombre', 'nombre_corto', 'presentacion', 'enfoque_alcance', 'unidad_responsable',
        'resolucion_numero', 'resolucion_fecha', 'resolucion_resumen',
        'periodicidad', 'modalidad', 'idiomas', 'tipos_contribucion',
        'sistema_arbitraje', 'norma_citacion', 'normas_publicacion',
        'issn', 'contacto_email', 'activo', 'publicado',
        'contenido_politicas', 'contenido_sobre', 'contenido_indexacion',
        'contenido_privacidad', 'contenido_preservacion', 'introduccion_envios',
        'facebook_url', 'whatsapp_url',
        'estado_editorial',
    ];

    protected $casts = [
        'resolucion_fecha' => 'date',
        'idiomas' => 'array',
        'tipos_contribucion' => 'array',
        'activo' => 'boolean',
        'publicado' => 'boolean',
    ];

    public function numeros(): HasMany
    {
        return $this->hasMany(RevistaNumero::class);
    }

    public function miembros(): HasMany
    {
        return $this->hasMany(RevistaMiembro::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(RevistaDocumento::class);
    }

    public function avisos(): HasMany
    {
        return $this->hasMany(RevistaAviso::class);
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(RevistaContacto::class);
    }

    public function lineasInvestigacion(): HasMany
    {
        return $this->hasMany(RevistaLineaInvestigacion::class);
    }

    public function envios(): HasMany
    {
        return $this->hasMany(RevistaEnvio::class);
    }

    public function scopePublica(Builder $query): Builder
    {
        return $query->where('activo', true)->where('publicado', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('resolucion')->singleFile()->acceptsMimeTypes(['application/pdf']);
    }

    public function getResolutionUrlAttribute(): string
    {
        $fallbackUrl = asset(self::RESOLUTION_PUBLIC_PATH);

        if (! config('media.uploads_enabled')) {
            return $fallbackUrl;
        }

        return $this->getFirstMediaUrl('resolucion') ?: $fallbackUrl;
    }

    protected static function booted(): void
    {
        static::creating(function (): void {
            if (static::query()->exists()) {
                throw ValidationException::withMessages(['nombre' => 'El portal administra una única ficha de revista.']);
            }
        });

        static::saving(function (Revista $revista): void {
            if (! $revista->publicado) {
                return;
            }

            $required = [
                'nombre' => 'nombre oficial',
                'nombre_corto' => 'nombre corto',
                'presentacion' => 'presentación',
                'unidad_responsable' => 'unidad responsable',
                'resolucion_numero' => 'número de resolución',
                'resolucion_fecha' => 'fecha de resolución',
                'resolucion_resumen' => 'resumen de la resolución',
                'periodicidad' => 'periodicidad',
                'contacto_email' => 'correo de contacto',
            ];

            foreach ($required as $field => $label) {
                if (blank($revista->{$field})) {
                    throw ValidationException::withMessages([$field => "El campo {$label} es obligatorio para publicar la revista."]);
                }
            }
        });
    }

    protected function editorialPublicationAttributes(): array
    {
        return ['activo', 'publicado'];
    }
}
