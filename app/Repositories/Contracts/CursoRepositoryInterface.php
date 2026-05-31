<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CursoRepositoryInterface extends RepositoryInterface
{
    /**
     * Todos los cursos ordenados por ciclo y orden.
     */
    public function ordenados(): Collection;
}
