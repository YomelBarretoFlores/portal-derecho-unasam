<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\View\View;

class DocenteController extends Controller
{
    public function index(): View
    {
        return view('docentes.index', [
            'docentes' => MockData::docentes(),
        ]);
    }
}
