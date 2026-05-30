<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface DocumentoRepositoryInterface extends RepositoryInterface
{
    public function ordenados(): Collection;
}
