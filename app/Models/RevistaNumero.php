<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RevistaNumero extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia;

    protected $table = 'revista_numeros';

    protected $fillable = [
        'revista_id', 'volumen', 'numero', 'slug', 'titulo', 'subtitulo',
        'descripcion', 'fecha_publicacion', 'orden', 'publicado',
        'estado_editorial',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'orden' => 'integer',
        'publicado' => 'boolean',
    ];

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class)->orderBy('orden')->orderBy('fecha');
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query
            ->where('publicado', true)
            ->whereNotNull('fecha_publicacion')
            ->whereDate('fecha_publicacion', '<=', today())
            ->whereHas('revista', fn (Builder $revista) => $revista->publica());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portada')->singleFile();
        $this->addMediaCollection('numero_pdf')->singleFile()->acceptsMimeTypes(['application/pdf']);
    }

    protected static function booted(): void
    {
        static::creating(function (self $numero): void {
            $numero->slug ??= Str::slug("volumen-{$numero->volumen}-numero-{$numero->numero}");
        });

        static::saving(function (self $numero): void {
            if ($numero->publicado && (blank($numero->volumen) || blank($numero->numero) || blank($numero->titulo) || blank($numero->fecha_publicacion))) {
                throw ValidationException::withMessages(['publicado' => 'Completa volumen, número, título y fecha antes de publicar.']);
            }
        });
    }
}
