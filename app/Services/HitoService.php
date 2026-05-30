<?php

namespace App\Services;

use App\Repositories\Contracts\HitoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class HitoService
{
    public function __construct(
        private readonly HitoRepositoryInterface $hitos,
    ) {
    }

    public function listado(): Collection
    {
        return $this->hitos->ordenados();
    }
}
