<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use App\Models\Concerns\ResuelveMedios;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RevistaDocumento extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia, ResuelveMedios;

    // Solo estas dos categorías tienen salida pública: /revista/normas-para-autores
    // y /revista/formatos-y-plantillas. Las políticas editoriales se editan como
    // texto enriquecido en la ficha de la revista (contenido_politicas).
    public const CATEGORIAS = ['norma' => 'Normas', 'formato' => 'Formatos y plantillas'];

    protected $table = 'revista_documentos';

    protected $fillable = ['revista_id', 'categoria', 'titulo', 'descripcion', 'url', 'orden', 'visible', 'estado_editorial'];

    protected $casts = ['orden' => 'integer', 'visible' => 'boolean'];

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function scopePublicos(Builder $query): Builder
    {
        return $query->where('visible', true)->where('estado_editorial', 'published');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('archivo')->singleFile();
    }

    protected function editorialPublicationAttributes(): array
    {
        return [];
    }

    public function getDownloadUrlAttribute(): string
    {
        return $this->resolverMedia('archivo', (string) $this->url);
    }
}
