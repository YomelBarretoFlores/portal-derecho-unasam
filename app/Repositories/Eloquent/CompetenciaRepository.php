<?php

namespace App\Repositories\Eloquent;

use App\Models\Competencia;
use App\Repositories\Contracts\CompetenciaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CompetenciaRepository extends BaseRepository implements CompetenciaRepositoryInterface
{
    public function __construct(Competencia $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->model->orderBy('orden')->get();
    }
}
