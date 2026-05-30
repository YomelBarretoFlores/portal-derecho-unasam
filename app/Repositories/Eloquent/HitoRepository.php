<?php

namespace App\Repositories\Eloquent;

use App\Models\Hito;
use App\Repositories\Contracts\HitoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class HitoRepository extends BaseRepository implements HitoRepositoryInterface
{
    public function __construct(Hito $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->model->orderBy('orden')->orderBy('anio')->get();
    }
}
