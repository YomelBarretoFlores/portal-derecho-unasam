<?php

namespace App\Services;

use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Lógica de negocio de Comunicados.
 *
 * Depende de la INTERFAZ del repositorio (inyectada por el contenedor),
 * nunca de Eloquent ni de la base de datos. Aquí va la lógica de la app;
 * los controladores y Livewire solo llaman a estos métodos.
 */
class ComunicadoService
{
    public function __construct(
        private readonly ComunicadoRepositoryInterface $comunicados,
    ) {
    }

    /**
     * Comunicados visibles en el sitio público.
     */
    public function listadoPublico(): Collection
    {
        return $this->comunicados->publicados();
    }
}
