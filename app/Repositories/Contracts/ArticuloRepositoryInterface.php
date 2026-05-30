<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ArticuloRepositoryInterface extends RepositoryInterface
{
    /**
     * Artículos publicados, del más reciente al más antiguo.
     */
    public function publicados(): Collection;

    /**
     * Lista de categorías presentes en los artículos publicados (para filtros).
     *
     * @return array<int, string>
     */
    public function categorias(): array;
}
