<?php

namespace App\Services;

use App\Repositories\Contracts\ArticuloRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RevistaService
{
    public function __construct(
        private readonly ArticuloRepositoryInterface $articulos,
    ) {
    }

    /**
     * Artículos visibles en el sitio público.
     */
    public function listadoPublico(): Collection
    {
        return $this->articulos->publicados();
    }

    /**
     * Categorías disponibles para los filtros.
     *
     * @return array<int, string>
     */
    public function categorias(): array
    {
        return $this->articulos->categorias();
    }
}
