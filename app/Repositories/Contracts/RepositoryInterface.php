<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Contrato base de todo repositorio.
 *
 * La lógica de negocio (Services) depende de ESTA interfaz, nunca de Eloquent.
 * Si algún día se cambia de ORM o base de datos, solo se reescribe la
 * implementación en App\Repositories\Eloquent — el resto de la app no se entera.
 */
interface RepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;
}
