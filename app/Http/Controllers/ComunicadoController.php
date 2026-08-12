<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Services\PublicContentCache;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComunicadoController extends Controller
{
    public function index(Request $request, PublicContentCache $content): View
    {
        return view('comunicados.index', [
            'comunicados' => $content->comunicados($request->integer('page', 1))->withQueryString(),
        ]);
    }

    public function show(Comunicado $comunicado): View
    {
        abort_unless(Comunicado::query()->publicados()->whereKey($comunicado->getKey())->exists(), 404);
        $comunicado->load('media');

        return view('comunicados.show', compact('comunicado'));
    }
}
