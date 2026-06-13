<?php

namespace App\Services;

use App\Repositories\Contracts\AccesoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AccesoService
{
    public function __construct(
        private readonly AccesoRepositoryInterface $accesos,
    ) {
    }

    public function listadoPublico(): Collection
    {
        return $this->accesos->activos();
    }
}
