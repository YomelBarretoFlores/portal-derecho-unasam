<?php

namespace App\Repositories\Eloquent;

use App\Models\Comunicado;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ComunicadoRepository extends BaseRepository implements ComunicadoRepositoryInterface
{
    public function __construct(Comunicado $model)
    {
        parent::__construct($model);
    }

    public function publicados(): Collection
    {
        return $this->model
            ->where('publicado', true)
            ->orderByDesc('fecha_publicacion')
            ->get();
    }
}
