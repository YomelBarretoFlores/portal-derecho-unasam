<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Página de inicio con todas sus secciones.
     *
     * Fase UI: los datos vienen de MockData. Al conectar el CMS, se reemplaza
     * MockData por los Services/Repositories sin tocar las vistas.
     */
    public function index(): View
    {
        return view('home', [
            'revista' => MockData::revistaArticulos()->take(4),
            'posts' => MockData::blogPosts()->take(4),
            'matriculados' => MockData::estadisticas('matriculados'),
        ]);
    }
}
