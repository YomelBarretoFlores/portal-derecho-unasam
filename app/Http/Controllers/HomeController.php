<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\EstadisticaService;
use App\Services\RevistaService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Página de inicio con todas sus secciones, alimentada desde el CMS.
     */
    public function index(
        RevistaService $revista,
        BlogService $blog,
        EstadisticaService $estadisticas,
    ): View {
        return view('home', [
            'revista' => $revista->listadoPublico()->take(4),
            'posts' => $blog->listadoPublico()->take(4),
            'matriculados' => $estadisticas->serie('matriculados'),
        ]);
    }
}
