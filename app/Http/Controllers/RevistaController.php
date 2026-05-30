<?php

namespace App\Http\Controllers;

use App\Services\RevistaService;
use Illuminate\View\View;

class RevistaController extends Controller
{
    public function index(RevistaService $revista): View
    {
        return view('revista.index', [
            'articulos' => $revista->listadoPublico(),
            'categorias' => $revista->categorias(),
        ]);
    }
}
