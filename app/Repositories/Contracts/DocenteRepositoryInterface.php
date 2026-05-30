<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface DocenteRepositoryInterface extends RepositoryInterface
{
    /**
     * Docentes activos, ordenados por el campo de orden.
     */
    public function activos(): Collection;
}
