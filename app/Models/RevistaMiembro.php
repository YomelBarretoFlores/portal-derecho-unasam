<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevistaMiembro extends Model
{
    public const GRUPOS = [
        'director_fundador' => 'Dirección fundadora',
        'editores' => 'Editores',
        'comite_editorial' => 'Comité editorial',
        'consejo_cientifico' => 'Consejo científico',
        'consejo_revisores' => 'Consejo de revisores',
        'correctores_estilo' => 'Correctores de estilo',
        'asistentes_editoriales' => 'Asistentes editoriales',
    ];

    protected $table = 'revista_miembros';

    protected $fillable = [
        'revista_id',
        'grupo',
        'grado',
        'nombre',
        'afiliacion',
        'pais',
        'orcid',
        'email',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query
            ->orderByRaw("case grupo
                when 'director_fundador' then 1
                when 'editores' then 2
                when 'comite_editorial' then 3
                when 'consejo_cientifico' then 4
                when 'consejo_revisores' then 5
                when 'correctores_estilo' then 6
                when 'asistentes_editoriales' then 7
                else 8 end")
            ->orderBy('orden')
            ->orderBy('nombre');
    }

    public function getGrupoLabelAttribute(): string
    {
        return self::GRUPOS[$this->grupo] ?? $this->grupo;
    }
}
