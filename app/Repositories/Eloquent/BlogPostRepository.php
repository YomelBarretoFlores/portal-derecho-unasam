<?php

namespace App\Repositories\Eloquent;

use App\Models\BlogPost;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    public function __construct(BlogPost $model)
    {
        parent::__construct($model);
    }

    public function publicados(): Collection
    {
        return $this->remember('blogposts.publicados', fn () => $this->model
            ->with('media')
            ->where('publicado', true)
            ->orderByDesc('fecha')
            ->get());
    }

    protected function serializeModel(Model $model): array
    {
        $data = $model->getAttributes();
        $data['_imagen_url'] = $model->getFirstMediaUrl('imagen', 'thumb');

        return $data;
    }
}
