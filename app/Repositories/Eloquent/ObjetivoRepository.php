<?php

namespace App\Repositories\Eloquent;

use App\Models\Objetivo;
use App\Repositories\Contracts\ObjetivoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ObjetivoRepository extends BaseRepository implements ObjetivoRepositoryInterface
{
    public function __construct(Objetivo $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->model->orderBy('orden')->get();
    }
}
