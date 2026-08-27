<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RevistaDocumento extends Model implements HasMedia
{
    use HasEditorialWorkflow, InteractsWithMedia;

    public const CATEGORIAS = ['norma' => 'Normas', 'formato' => 'Formatos y plantillas', 'politica' => 'Políticas editoriales', 'informativo' => 'Documentos informativos'];

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
        return $this->getFirstMediaUrl('archivo') ?: (string) $this->url;
    }
}
