<?php

namespace App\Services;

use App\Repositories\Contracts\CursoRepositoryInterface;
use Illuminate\Support\Collection;

class CursoService
{
    public function __construct(
        private readonly CursoRepositoryInterface $cursos,
    ) {
    }

    /**
     * Cursos agrupados por ciclo: Collection<int ciclo, Collection<Curso>>.
     */
    public function porCiclo(): Collection
    {
        return $this->cursos->ordenados()->groupBy('ciclo');
    }
}
