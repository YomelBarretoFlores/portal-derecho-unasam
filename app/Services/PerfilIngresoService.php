<?php

namespace App\Services;

use App\Repositories\Contracts\PerfilIngresoAreaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PerfilIngresoService
{
    public function __construct(
        private readonly PerfilIngresoAreaRepositoryInterface $areas,
    ) {
    }

    public function areas(): Collection
    {
        return $this->areas->ordenados();
    }
}
