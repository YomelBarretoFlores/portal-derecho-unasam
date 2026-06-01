<?php

namespace App\Repositories\Eloquent;

use App\Models\Documento;
use App\Repositories\Contracts\DocumentoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DocumentoRepository extends BaseRepository implements DocumentoRepositoryInterface
{
    public function __construct(Documento $model)
    {
        parent::__construct($model);
    }

    public function ordenados(): Collection
    {
        return $this->remember('documentos.ordenados', fn () => $this->model
            ->with('media')
            ->orderBy('orden')
            ->orderByDesc('fecha')
            ->get());
    }

    protected function serializeModel(Model $model): array
    {
        $data = $model->getAttributes();
        $data['_enlace'] = $model->enlace;

        return $data;
    }
}
