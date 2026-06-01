<?php

namespace App\Repositories\Eloquent;

use App\Models\Docente;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DocenteRepository extends BaseRepository implements DocenteRepositoryInterface
{
    public function __construct(Docente $model)
    {
        parent::__construct($model);
    }

    public function activos(): Collection
    {
        return $this->remember('docentes.activos', fn () => $this->model
            ->with('media')
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('name')
            ->get());
    }

    protected function serializeModel(Model $model): array
    {
        $data = $model->getAttributes();
        $data['_foto_url'] = $model->getFirstMediaUrl('foto', 'thumb');

        return $data;
    }
}
