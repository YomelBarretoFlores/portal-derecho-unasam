<?php

namespace App\Services;

use App\Repositories\Contracts\EstadisticaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EstadisticaService
{
    public function __construct(
        private readonly EstadisticaRepositoryInterface $estadisticas,
    ) {
    }

    /**
     * Serie de un tipo de indicador (ordenada por año).
     */
    public function serie(string $tipo): Collection
    {
        return $this->estadisticas->serie($tipo);
    }
}
