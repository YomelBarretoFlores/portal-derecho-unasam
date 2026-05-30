<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CompetenciaRepositoryInterface extends RepositoryInterface
{
    public function ordenados(): Collection;
}
