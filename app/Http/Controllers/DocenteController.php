<?php

namespace App\Http\Controllers;

use App\Services\DocenteService;
use Illuminate\View\View;

class DocenteController extends Controller
{
    public function index(DocenteService $docentes): View
    {
        return view('docentes.index', [
            'docentes' => $docentes->listadoPublico(),
        ]);
    }
}
