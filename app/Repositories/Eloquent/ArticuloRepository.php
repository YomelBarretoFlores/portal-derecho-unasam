<?php

namespace App\Repositories\Eloquent;

use App\Models\Articulo;
use App\Repositories\Contracts\ArticuloRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ArticuloRepository extends BaseRepository implements ArticuloRepositoryInterface
{
    public function __construct(Articulo $model)
    {
        parent::__construct($model);
    }

    public function publicados(): Collection
    {
        return $this->model
            ->where('publicado', true)
            ->orderByDesc('fecha')
            ->get();
    }

    public function categorias(): array
    {
        return $this->model
            ->where('publicado', true)
            ->whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria')
            ->all();
    }
}
