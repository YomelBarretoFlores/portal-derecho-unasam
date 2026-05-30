<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ObjetivoRepositoryInterface extends RepositoryInterface
{
    public function ordenados(): Collection;
}
