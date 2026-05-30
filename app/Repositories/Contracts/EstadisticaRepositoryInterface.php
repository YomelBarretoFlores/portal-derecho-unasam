<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EstadisticaRepositoryInterface extends RepositoryInterface
{
    /**
     * Serie de un tipo de indicador, ordenada por año ascendente.
     */
    public function serie(string $tipo): Collection;
}
