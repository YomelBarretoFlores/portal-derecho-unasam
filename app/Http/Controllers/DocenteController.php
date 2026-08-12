<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Services\PublicContentCache;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocenteController extends Controller
{
    public function index(Request $request, PublicContentCache $content): View
    {
        return view('docentes.index', [
            'docentes' => $content->docentes($request->integer('page', 1))->withQueryString(),
        ]);
    }

    public function show(Docente $docente): View
    {
        abort_unless(
            $docente->activo && $docente->estado_revision === 'verified',
            404,
        );

        $docente->loadMissing('media');

        return view('docentes.show', compact('docente'));
    }
}
