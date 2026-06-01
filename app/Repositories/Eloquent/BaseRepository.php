<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
     * @template T
     * @param  \Closure():T  $callback
     * @return T
     */
    protected function remember(string $key, \Closure $callback): mixed
    {
        $version = Cache::get('content.version', 1);
        $cacheKey = "repo:{$key}:v{$version}";

        $cached = Cache::get($cacheKey);

        if (is_array($cached) && isset($cached['_type'])) {
            return $cached['_type'] === 'collection'
                ? $this->hydrate($cached['_data'])
                : $cached['_data'];
        }

        if ($cached !== null) {
            Log::warning("Caché corrupta [{$cacheKey}], recomputando desde BD.");
        }

        $fresh = $callback();

        Cache::put($cacheKey, [
            '_type' => $fresh instanceof Collection ? 'collection' : 'raw',
            '_data' => $fresh instanceof Collection
                ? $fresh->map(fn (Model $m) => $this->serializeModel($m))->values()->all()
                : $fresh,
        ], static::CACHE_TTL);

        return $fresh;
    }

    protected function serializeModel(Model $model): array
    {
        return $model->getAttributes();
    }

    private function hydrate(array $rows): Collection
    {
        return new Collection(array_map(function (array $row) {
            $instance = $this->model->newInstance([], exists: true);
            $instance->setRawAttributes($row, true);

            return $instance;
        }, $rows));
    }
}
