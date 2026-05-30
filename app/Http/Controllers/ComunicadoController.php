<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\View\View;

class ComunicadoController extends Controller
{
    public function index(): View
    {
        return view('comunicados.index', [
            'comunicados' => MockData::comunicados(),
        ]);
    }
}
