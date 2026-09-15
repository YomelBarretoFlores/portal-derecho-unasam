<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasEditorialWorkflow;

    protected $fillable = [
        'plan',
        'ciclo',
        'nombre',
        'creditos',
        'tipo',
        'orden',
        'publicado',
        'estado_editorial',
    ];

    protected $casts = [
        'ciclo' => 'integer',
        'creditos' => 'integer',
        'orden' => 'integer',
        'publicado' => 'boolean',
    ];

    public function scopePublicados(Builder $query, string $plan = '2023'): Builder
    {
        return $query
            ->where('plan', $plan)
            ->where('publicado', true)
            ->orderBy('ciclo')
            ->orderBy('orden')
            ->orderBy('nombre');
    }

    /**
     * Ciclos que tiene la malla. Seis años de carrera, dos ciclos por año.
     *
     * Vive aquí y no en el formulario para que el panel y la etiqueta en
     * romanos no puedan volver a discrepar: cuando discrepaban, el panel
     * no dejaba registrar los ciclos XI y XII.
     */
    public const CICLOS = 12;

    /**
     * Tipos de curso para el select del panel.
     */
    public const TIPOS = [
        'General' => 'General',
        'Específico' => 'Específico',
        'Especialidad' => 'Especialidad',
        'Electivo' => 'Electivo',
    ];

    /**
     * Etiqueta legible del ciclo en números romanos (I–XII).
     *
     * La malla vigente de Derecho llega hasta el ciclo XII, no hasta el X:
     * son seis años. Quedarse en diez dejaba los dos últimos ciclos sin
     * etiqueta legible, cayendo al número árabe entre romanos.
     */
    public function getCicloRomanoAttribute(): string
    {
        $romanos = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        return $romanos[$this->ciclo] ?? (string) $this->ciclo;
    }
}
