<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Implementación Eloquent del contrato base.
 *
 * ESTA es la ÚNICA capa que toca la base de datos directamente.
 * Cualquier repositorio concreto (ComunicadoRepository, DocenteRepository...)
 * extiende esta clase y solo agrega sus consultas específicas.
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected const CACHE_TTL = 600;

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
        $cacheKey = "repo:{$key}:v{$version}";

        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            if ($this->cacheEsValida($cached)) {
                return $cached;
            }

            Log::warning("Caché corrupta [{$cacheKey}], recomputando desde BD.");
        }

        $fresh = $callback();
        Cache::put($cacheKey, $fresh, static::CACHE_TTL);

        return $fresh;
    }

    /**
     * ¿El valor recuperado de caché es utilizable (no un objeto incompleto)?
     */
    private function cacheEsValida(mixed $valor): bool
    {
        if ($valor instanceof \__PHP_Incomplete_Class) {
            return false;
        }

        if (is_iterable($valor)) {
            foreach ($valor as $item) {
                if ($item instanceof \__PHP_Incomplete_Class) {
                    return false;
                }
            }
        }

        return true;
    }
}
