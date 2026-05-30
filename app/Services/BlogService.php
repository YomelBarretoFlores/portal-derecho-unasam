<?php

namespace App\Services;

use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BlogService
{
    public function __construct(
        private readonly BlogPostRepositoryInterface $posts,
    ) {
    }

    /**
     * Publicaciones visibles en el sitio público.
     */
    public function listadoPublico(): Collection
    {
        return $this->posts->publicados();
    }
}
