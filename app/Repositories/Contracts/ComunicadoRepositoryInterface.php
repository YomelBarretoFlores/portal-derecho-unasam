<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ComunicadoRepositoryInterface extends RepositoryInterface
{
    /**
     * Comunicados publicados, ordenados del más reciente al más antiguo.
     */
    public function publicados(): Collection;
}
