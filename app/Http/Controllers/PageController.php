<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Páginas institucionales con contenido mayormente estático.
 * Cada método devuelve su vista en resources/views/pages/.
 */
class PageController extends Controller
{
    public function presentacion(): View
    {
        return view('pages.presentacion');
    }

    public function historia(): View
    {
        return view('pages.historia');
    }

    public function mision(): View
    {
        return view('pages.mision');
    }

    public function campoLaboral(): View
    {
        return view('pages.campo-laboral');
    }

    public function objetivos(): View
    {
        return view('pages.objetivos');
    }

    public function resumen(): View
    {
        return view('pages.resumen');
    }

    public function competencias(): View
    {
        return view('pages.competencias');
    }

    public function perfilIngreso(): View
    {
        return view('pages.perfil-ingreso');
    }

    public function perfilEgreso(): View
    {
        return view('pages.perfil-egreso');
    }

    public function planEstudios2023(): View
    {
        return view('pages.plan-2023');
    }

    public function planEstudios2019(): View
    {
        return $this->generica('Plan de Estudios 2019', 'Académico');
    }

    public function organigrama(): View
    {
        return $this->generica('Organigrama', 'Institucional');
    }

    public function documentos(): View
    {
        return view('pages.documentos');
    }

    /**
     * Plantilla genérica reutilizable para páginas placeholder.
     */
    private function generica(string $titulo, string $seccion): View
    {
        return view('pages.generic', [
            'titulo' => $titulo,
            'seccion' => $seccion,
        ]);
    }
}
