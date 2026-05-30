<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface BlogPostRepositoryInterface extends RepositoryInterface
{
    /**
     * Publicaciones publicadas, de la más reciente a la más antigua.
     */
    public function publicados(): Collection;
}
