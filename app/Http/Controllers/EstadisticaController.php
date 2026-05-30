<?php

namespace App\Http\Controllers;

use App\Models\Estadistica;
use App\Services\EstadisticaService;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EstadisticaController extends Controller
{
    public function show(string $tipo, EstadisticaService $estadisticas): View
    {
        $tipos = Estadistica::TIPOS;

        if (! array_key_exists($tipo, $tipos)) {
            throw new NotFoundHttpException("Estadística desconocida: {$tipo}");
        }

        return view('estadisticas.show', [
            'tipo' => $tipo,
            'titulo' => $tipos[$tipo],
            'tipos' => $tipos,
            'serie' => $estadisticas->serie($tipo),
        ]);
    }
}
