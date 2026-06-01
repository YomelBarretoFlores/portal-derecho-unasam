<?php

namespace App\Repositories\Eloquent;

use App\Models\Articulo;
use App\Repositories\Contracts\ArticuloRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ArticuloRepository extends BaseRepository implements ArticuloRepositoryInterface
{
    public function __construct(Articulo $model)
    {
        parent::__construct($model);
    }

    public function publicados(): Collection
    {
        return $this->remember('articulos.publicados', fn () => $this->model
            ->with('media')
            ->where('publicado', true)
            ->orderByDesc('fecha')
            ->get());
    }

    public function categorias(): array
    {
        return $this->remember('articulos.categorias', fn () => $this->model
            ->where('publicado', true)
            ->whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria')
            ->all());
    }

    protected function serializeModel(Model $model): array
    {
        $data = $model->getAttributes();
        $data['_pdf_url'] = $model->getFirstMediaUrl('pdf');

        return $data;
    }
}
