<?php

namespace App\Repositories\Eloquent;

use App\Models\AreaLaboral;
use App\Repositories\Contracts\AreaLaboralRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AreaLaboralRepository extends BaseRepository implements AreaLaboralRepositoryInterface
{
    public function __construct(AreaLaboral $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->model->orderBy('orden')->get();
    }
}
