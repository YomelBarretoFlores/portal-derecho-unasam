<?php

namespace App\Services;

use App\Repositories\Contracts\AreaLaboralRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AreaLaboralService
{
    public function __construct(
        private readonly AreaLaboralRepositoryInterface $areas,
    ) {
    }

    public function listado(): Collection
    {
        return $this->areas->ordenados();
    }
}
