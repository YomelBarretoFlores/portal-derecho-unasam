<?php

namespace App\Services;

use App\Models\Curso;
use Illuminate\Support\Collection;

class CursoService
{
    /**
     * Cursos agrupados por ciclo: Collection<int ciclo, Collection<Curso>>.
     */
    public function porCiclo(string $plan = '2023'): Collection
    {
        return Curso::query()->publicados($plan)->get()->groupBy('ciclo');
    }
}
