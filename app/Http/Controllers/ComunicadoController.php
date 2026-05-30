<?php

namespace App\Http\Controllers;

use App\Services\ComunicadoService;
use Illuminate\View\View;

class ComunicadoController extends Controller
{
    public function index(ComunicadoService $comunicados): View
    {
        return view('comunicados.index', [
            'comunicados' => $comunicados->listadoPublico(),
        ]);
    }
}
