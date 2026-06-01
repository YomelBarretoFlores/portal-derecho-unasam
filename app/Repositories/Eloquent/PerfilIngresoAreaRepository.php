<?php

namespace App\Repositories\Eloquent;

use App\Models\PerfilIngresoArea;
use App\Repositories\Contracts\PerfilIngresoAreaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PerfilIngresoAreaRepository extends BaseRepository implements PerfilIngresoAreaRepositoryInterface
{
    public function __construct(PerfilIngresoArea $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->remember('perfil_ingreso_areas.ordenados', fn () => $this->model->orderBy('orden')->get());
    }
}
