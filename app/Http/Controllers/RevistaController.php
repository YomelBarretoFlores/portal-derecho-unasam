<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\RevistaNumero;
use App\Services\PublicContentCache;
use App\Services\RevistaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevistaController extends Controller
{
    public function index(Request $request, PublicContentCache $content): View
    {
        $q = trim($request->string('q')->toString());

        return view('revista.index', [
            ...$content->revista($q, $request->integer('page', 1)),
            'q' => $q,
        ]);
    }

    public function numero(Request $request, RevistaNumero $numero, RevistaService $revista): View
    {
        abort_unless(RevistaNumero::query()->publicados()->whereKey($numero->getKey())->exists(), 404);
        $numero->load(['revista', 'media']);
        $categoria = trim($request->string('categoria')->toString());
        $q = trim($request->string('q')->toString());

        return view('revista.numero', [
            'numero' => $numero,
            'articulos' => $revista->articulos($numero, $q, $categoria),
            'categorias' => Articulo::query()->publicados()->where('revista_numero_id', $numero->id)
                ->distinct()->orderBy('categoria')->pluck('categoria'),
            'categoria' => $categoria,
            'q' => $q,
        ]);
    }

    public function equipo(RevistaService $revista): View
    {
        $ficha = $revista->revistaPublica();
        abort_unless($ficha, 404);

        return view('revista.equipo', [
            'revista' => $ficha,
            'grupos' => $ficha->miembros->groupBy('grupo'),
        ]);
    }

    public function normas(RevistaService $revista): View
    {
        $ficha = $revista->revistaPublica();
        abort_unless($ficha && filled($ficha->normas_publicacion), 404);

        return view('revista.normas', ['revista' => $ficha]);
    }

    public function articulo(RevistaNumero $numero, Articulo $articulo): View
    {
        abort_unless(
            RevistaNumero::query()->publicados()->whereKey($numero->getKey())->exists()
            && Articulo::query()->publicados()->where('revista_numero_id', $numero->id)->whereKey($articulo->getKey())->exists(),
            404,
        );
        $articulo->load(['numero.revista', 'media']);

        return view('revista.articulo', compact('numero', 'articulo'));
    }
}
