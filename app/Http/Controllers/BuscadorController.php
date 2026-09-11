<?php

namespace App\Http\Controllers;

use App\Services\Buscador;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuscadorController extends Controller
{
    public function __invoke(Request $request, Buscador $buscador): View
    {
        $q = trim($request->string('q')->toString());
        // Recorte defensivo: la consulta se refleja en la página, así que no
        // conviene aceptar cadenas de longitud arbitraria.
        $q = mb_substr($q, 0, 120);

        return view('buscar', [
            'q' => $q,
            'resultados' => $q === '' ? collect() : $buscador->buscar($q),
        ]);
    }
}
