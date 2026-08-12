<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Services\PublicContentCache;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request, PublicContentCache $content): View
    {
        $tipo = $request->string('tipo')->toString();

        return view('blog.index', [
            'posts' => $content->blog($tipo, $request->integer('page', 1))->withQueryString(),
            'tipo' => $tipo,
        ]);
    }

    public function show(BlogPost $post): View
    {
        abort_unless(BlogPost::query()->publicados()->whereKey($post->getKey())->exists(), 404);
        $post->load('media');

        return view('blog.show', compact('post'));
    }
}
