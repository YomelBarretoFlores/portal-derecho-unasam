<?php

namespace App\Repositories\Eloquent;

use App\Models\Documento;
use App\Repositories\Contracts\DocumentoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocumentoRepository extends BaseRepository implements DocumentoRepositoryInterface
{
    public function __construct(Documento $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->model->orderBy('orden')->orderByDesc('fecha')->get();
    }
}
