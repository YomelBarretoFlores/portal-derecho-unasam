<?php

namespace App\Repositories\Eloquent;

use App\Models\Acceso;
use App\Repositories\Contracts\AccesoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AccesoRepository extends BaseRepository implements AccesoRepositoryInterface
{
    public function __construct(Acceso $model)
    {
        parent::__construct($model);
    }

    public function activos(): Collection
    {
        return $this->remember('accesos.ordenados', fn () => $this->model
            ->where('activo', true)
            ->orderBy('orden')
            ->get());
    }
}
