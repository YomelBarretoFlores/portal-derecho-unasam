<?php

namespace App\Repositories\Eloquent;

use App\Models\BlogPost;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    public function __construct(BlogPost $model)
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
}
