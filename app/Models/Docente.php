<?php

namespace App\Models;

use App\Enums\EditorialStatus;
use App\Models\Concerns\HasEditorialWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Docente extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia;

    public const ESTADOS_REVISION = [
        'pending' => 'Pendiente de revisión',
        'verified' => 'Verificado',
    ];

    protected $fillable = [
        'name',
        'slug',
        'area',
        'grado',
        'categoria',
        'dedicacion',
        'resena',
        'email_institucional',
        'orcid',
        'google_scholar_url',
        'cti_vitae_url',
        'perfil_academico_url',
        'publicaciones',
        'estado_revision',
        'documento_fuente',
        'observaciones_revision',
        'orden',
        'activo',
        'estado_editorial',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
        'publicaciones' => 'array',
    ];

    public function scopeActivos(Builder $query): Builder
    {
        return $query->publicos()->orderBy('orden')->orderBy('name');
    }

    public function scopePublicos(Builder $query): Builder
    {
        return $query
            ->where('activo', true)
            ->where('estado_revision', 'verified');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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
        $iniciales = Str::substr($palabras->first(), 0, 1).Str::substr($palabras->last(), 0, 1);

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

    protected static function booted(): void
    {
        static::creating(function (self $docente): void {
            $docente->slug ??= self::uniqueSlug($docente->name);
        });

        static::saving(function (self $docente): void {
            if (in_array($docente->estado_editorial, [EditorialStatus::Verified->value, EditorialStatus::Published->value], true)
                && blank($docente->documento_fuente)) {
                throw ValidationException::withMessages([
                    'estado_editorial' => 'Registra el documento fuente antes de verificar o publicar el perfil.',
                ]);
            }

            $docente->estado_revision = in_array($docente->estado_editorial, [EditorialStatus::Verified->value, EditorialStatus::Published->value], true)
                ? 'verified'
                : 'pending';

            if (! $docente->activo) {
                return;
            }

            $missing = collect([
                'name' => $docente->name,
                'slug' => $docente->slug,
                'categoria' => $docente->categoria,
                'dedicacion' => $docente->dedicacion,
                'resena' => $docente->resena,
            ])->filter(fn (mixed $value): bool => blank($value))->keys();

            if ($docente->estado_revision !== 'verified' || $missing->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'activo' => $docente->estado_revision !== 'verified'
                        ? 'El perfil debe estar verificado antes de publicarse.'
                        : 'Antes de publicar completa: '.$missing->implode(', ').'.',
                ]);
            }
        });
    }

    protected function editorialPublicationAttributes(): array
    {
        return ['activo'];
    }

    protected function resolveInitialEditorialStatus(): string
    {
        if ($this->activo && $this->estado_revision !== 'verified') {
            throw ValidationException::withMessages([
                'activo' => 'El perfil debe estar verificado antes de publicarse.',
            ]);
        }

        if ($this->activo && $this->estado_revision === 'verified') {
            return EditorialStatus::Published->value;
        }

        if ($this->estado_revision === 'verified') {
            return EditorialStatus::Verified->value;
        }

        return filled($this->documento_fuente)
            ? EditorialStatus::Pending->value
            : EditorialStatus::Draft->value;
    }

    private static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'docente';
        $slug = $base;
        $suffix = 2;

        while (self::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
