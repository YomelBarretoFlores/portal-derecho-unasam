<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface AreaLaboralRepositoryInterface extends RepositoryInterface
{
    public function ordenados(): Collection;
}
