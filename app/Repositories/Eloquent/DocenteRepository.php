<?php

namespace App\Repositories\Eloquent;

use App\Models\Docente;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocenteRepository extends BaseRepository implements DocenteRepositoryInterface
{
    public function __construct(Docente $model)
    {
        parent::__construct($model);
    }

    public function activos(): Collection
    {
        return $this->model
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('name')
            ->get();
    }
}
