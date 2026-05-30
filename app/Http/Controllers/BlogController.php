<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(BlogService $blog): View
    {
        return view('blog.index', [
            'posts' => $blog->listadoPublico(),
        ]);
    }
}
