<?php

namespace App\Repositories\Eloquent;

use App\Models\Estadistica;
use App\Repositories\Contracts\EstadisticaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EstadisticaRepository extends BaseRepository implements EstadisticaRepositoryInterface
{
    public function __construct(Estadistica $model)
    {
        parent::__construct($model);
    }

    public function serie(string $tipo): Collection
    {
        return $this->model
            ->where('tipo', $tipo)
            ->orderBy('anio')
            ->get();
    }
}
