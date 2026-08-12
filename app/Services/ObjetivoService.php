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
                'destacado' => (bool) $items->first()->vigente,
                'objetivos' => $items->pluck('texto')->all(),
            ])
            ->values();
    }
}
