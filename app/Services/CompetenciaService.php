<?php

namespace App\Services;

use App\Models\Competencia;
use Illuminate\Support\Collection;

class CompetenciaService
{
    /**
     * Estructura por grupo (Generales/Específicas) y plan, compatible con la vista:
     * [['grupo','prefijo','planes'=>[['titulo','destacado','items'=>[['nombre','texto'],...]]]]].
     */
    public function grupos(): Collection
    {
        return Competencia::query()->orderBy('orden')->get()
            ->groupBy('grupo')
            ->map(fn ($filas, $grupo) => [
                'grupo' => 'Competencias '.$grupo,
                'prefijo' => str_starts_with($grupo, 'Espec') ? 'CE' : 'CG',
                'planes' => $filas->groupBy('plan')
                    ->map(fn ($items, $plan) => [
                        'titulo' => $plan,
                        'destacado' => (bool) $items->first()->vigente,
                        'items' => $items->map(fn ($c) => [
                            'nombre' => $c->nombre,
                            'texto' => $c->texto,
                        ])->all(),
                    ])
                    ->values(),
            ])
            ->values();
    }
}
