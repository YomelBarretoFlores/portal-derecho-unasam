<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        return view('blog.index', [
            'posts' => MockData::blogPosts(),
        ]);
    }
}
