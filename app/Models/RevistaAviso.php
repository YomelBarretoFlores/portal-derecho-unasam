<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use App\Models\Concerns\ResuelveMedios;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RevistaAviso extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia, ResuelveMedios;

    protected $table = 'revista_avisos';

    protected $fillable = ['revista_id', 'titulo', 'slug', 'resumen', 'contenido', 'fecha_publicacion', 'fecha_caducidad', 'enlace', 'estado_editorial', 'adjunto_url_respaldo'];

    protected $casts = ['fecha_publicacion' => 'datetime', 'fecha_caducidad' => 'datetime'];

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query->where('estado_editorial', 'published')->whereNotNull('fecha_publicacion')->where('fecha_publicacion', '<=', now())->where(fn (Builder $q) => $q->whereNull('fecha_caducidad')->orWhere('fecha_caducidad', '>=', now()));
    }

    /** Adjunto del aviso: archivo subido o URL pública de respaldo. */
    public function getAdjuntoUrlAttribute(): string
    {
        return $this->resolverMedia('adjunto', (string) $this->adjunto_url_respaldo);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('adjunto')->singleFile();
    }

    protected function editorialPublicationAttributes(): array
    {
        return [];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $aviso) => $aviso->slug ??= Str::slug($aviso->titulo));
    }
}
