<?php

namespace App\Repositories\Eloquent;

use App\Models\Comunicado;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ComunicadoRepository extends BaseRepository implements ComunicadoRepositoryInterface
{
    public function __construct(Comunicado $model)
    {
        parent::__construct($model);
    }

    public function publicados(): Collection
    {
        return $this->remember('comunicados.publicados', fn () => $this->model
            ->with('media')
            ->where('publicado', true)
            ->orderByDesc('fecha_publicacion')
            ->get());
    }

    protected function serializeModel(Model $model): array
    {
        $data = $model->getAttributes();
        $data['_imagen_url'] = $model->getFirstMediaUrl('imagen', 'thumb');

        return $data;
    }
}
