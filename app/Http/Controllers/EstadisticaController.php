<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EstadisticaController extends Controller
{
    public function show(string $tipo): View
    {
        $tipos = MockData::tiposEstadistica();

        if (! array_key_exists($tipo, $tipos)) {
            throw new NotFoundHttpException("Estadística desconocida: {$tipo}");
        }

        return view('estadisticas.show', [
            'tipo' => $tipo,
            'titulo' => $tipos[$tipo],
            'tipos' => $tipos,
            'serie' => MockData::estadisticas($tipo),
        ]);
    }
}
