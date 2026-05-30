<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PerfilIngresoAreaRepositoryInterface extends RepositoryInterface
{
    public function ordenados(): Collection;
}
