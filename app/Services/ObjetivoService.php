<?php

namespace App\Services;

use App\Models\Objetivo;
use Illuminate\Support\Collection;

class ObjetivoService
{
    /**
     * Objetivos agrupados por plan: [['titulo','destacado','objetivos'=>[texto,...]], ...].
     */
    public function planes(): Collection
    {
        return Objetivo::query()->orderBy('orden')->get()
            ->groupBy('plan')
            ->map(fn ($items, $plan) => [
                'titulo' => $plan,
                // Cualquier fila marcada como vigente marca el plan: el panel ofrece el
                // interruptor en todas, así que leer solo la primera lo volvía decorativo.
                'destacado' => $items->contains(fn ($item): bool => (bool) $item->vigente),
                'objetivos' => $items->pluck('texto')->all(),
            ])
            ->values();
    }
}
