<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\View\View;

class RevistaController extends Controller
{
    public function index(): View
    {
        return view('revista.index', [
            'articulos' => MockData::revistaArticulos(),
            'categorias' => MockData::categorias(),
        ]);
    }
}
