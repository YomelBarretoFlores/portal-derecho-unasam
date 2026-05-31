<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Implementación Eloquent del contrato base.
 *
 * ESTA es la ÚNICA capa que toca la base de datos directamente.
 * Cualquier repositorio concreto (ComunicadoRepository, DocenteRepository...)
 * extiende esta clase y solo agrega sus consultas específicas.
 */
abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    /**
     * Cachea una consulta de lectura del sitio público. La clave incluye la
     * "versión de contenido": cualquier guardado/borrado en el panel la
     * incrementa (ver AppServiceProvider), invalidando todo de inmediato.
     * Así el sitio no consulta Neon en cada navegación, pero los cambios del
     * CMS se ven al instante.
     *
     * @template T
     * @param  \Closure():T  $callback
     * @return T
     */
    protected function remember(string $key, \Closure $callback): mixed
    {
        $version = Cache::get('content.version', 1);

        return Cache::remember("repo:{$key}:v{$version}", 600, $callback);
    }
}
