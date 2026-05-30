<?php

namespace App\Services;

use App\Repositories\Contracts\DocenteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocenteService
{
    public function __construct(
        private readonly DocenteRepositoryInterface $docentes,
    ) {
    }

    /**
     * Docentes visibles en el sitio público.
     */
    public function listadoPublico(): Collection
    {
        return $this->docentes->activos();
    }
}
