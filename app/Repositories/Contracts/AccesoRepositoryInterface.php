<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface AccesoRepositoryInterface extends RepositoryInterface
{
    public function activos(): Collection;
}
