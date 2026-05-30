<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface HitoRepositoryInterface extends RepositoryInterface
{
    public function ordenados(): Collection;
}
