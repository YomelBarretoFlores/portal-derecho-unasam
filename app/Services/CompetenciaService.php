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
                        // Cualquier fila marcada como vigente marca el plan: el panel ofrece el
                        // interruptor en todas, así que leer solo la primera lo volvía decorativo.
                        'destacado' => $items->contains(fn ($item): bool => (bool) $item->vigente),
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
