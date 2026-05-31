<?php

namespace App\Repositories\Eloquent;

use App\Models\Curso;
use App\Repositories\Contracts\CursoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CursoRepository extends BaseRepository implements CursoRepositoryInterface
{
    public function __construct(Curso $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->remember('cursos.ordenados', fn () => $this->model->orderBy('ciclo')->orderBy('orden')->orderBy('nombre')->get());
    }
}
