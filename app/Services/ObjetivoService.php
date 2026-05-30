<?php

namespace App\Services;

use App\Repositories\Contracts\ObjetivoRepositoryInterface;
use Illuminate\Support\Collection;

class ObjetivoService
{
    public function __construct(
        private readonly ObjetivoRepositoryInterface $objetivos,
    ) {
    }

    /**
     * Objetivos agrupados por plan: [['titulo','destacado','objetivos'=>[texto,...]], ...].
     */
    public function planes(): Collection
    {
        return $this->objetivos->ordenados()
            ->groupBy('plan')
            ->map(fn ($items, $plan) => [
                'titulo' => $plan,
                'destacado' => (bool) $items->first()->vigente,
                'objetivos' => $items->pluck('texto')->all(),
            ])
            ->values();
    }
}
