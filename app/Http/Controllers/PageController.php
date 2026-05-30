<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AreaLaboralService;
use App\Services\CompetenciaService;
use App\Services\DocumentoService;
use App\Services\HitoService;
use App\Services\ObjetivoService;
use App\Services\PerfilIngresoService;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function presentacion(): View
    {
        return view('pages.presentacion', [
            'titulo' => Setting::get('presentacion_titulo'),
            'cuerpo' => Setting::get('presentacion_cuerpo'),
            'datos' => $this->parsearDatos(Setting::get('datos_programa')),
        ]);
    }

    public function resumen(): View
    {
        return view('pages.resumen', [
            'cuerpo' => Setting::get('resumen_cuerpo'),
            'cita1' => Setting::get('resumen_cita1'),
            'cita2' => Setting::get('resumen_cita2'),
            'cierre' => Setting::get('resumen_cierre'),
        ]);
    }

    public function historia(HitoService $hitos): View
    {
        return view('pages.historia', [
            'hitos' => $hitos->listado(),
            'trayectoriaTitulo' => Setting::get('historia_trayectoria_titulo'),
            'trayectoriaCuerpo' => Setting::get('historia_trayectoria_cuerpo'),
        ]);
    }

    public function mision(): View
    {
        return view('pages.mision', [
            'mision' => Setting::get('mision'),
            'vision' => Setting::get('vision'),
        ]);
    }

    public function campoLaboral(AreaLaboralService $areas): View
    {
        return view('pages.campo-laboral', [
            'areas' => $areas->listado(),
        ]);
    }

    public function objetivos(ObjetivoService $objetivos): View
    {
        return view('pages.objetivos', [
            'planes' => $objetivos->planes(),
        ]);
    }

    public function planEstudios2023(): View
    {
        return view('pages.plan-2023');
    }

    public function planEstudios2019(): View
    {
        return view('pages.generica', ['titulo' => 'Plan de Estudios 2019', 'seccion' => 'Académico']);
    }

    public function competencias(CompetenciaService $competencias): View
    {
        return view('pages.competencias', [
            'grupos' => $competencias->grupos(),
        ]);
    }

    public function perfilIngreso(PerfilIngresoService $perfil): View
    {
        return view('pages.perfil-ingreso', [
            'areas' => $perfil->areas(),
            'especifico' => Setting::get('perfil_ingreso_especifico'),
        ]);
    }

    public function perfilEgreso(): View
    {
        return view('pages.perfil-egreso', [
            'egreso2023' => Setting::get('perfil_egreso_2023'),
            'egreso2019' => Setting::get('perfil_egreso_2019'),
        ]);
    }

    public function organigrama(): View
    {
        return view('pages.generica', ['titulo' => 'Organigrama', 'seccion' => 'Institucional']);
    }

    public function documentos(DocumentoService $documentos): View
    {
        return view('pages.documentos', [
            'documentos' => $documentos->listado(),
        ]);
    }

    /**
     * Convierte el texto «Etiqueta | Valor» (una línea por dato) en pares.
     *
     * @return array<int, array{0:string,1:string}>
     */
    private function parsearDatos(?string $raw): array
    {
        if (blank($raw)) {
            return [];
        }

        return collect(preg_split('/\R/', trim($raw)))
            ->map(fn ($linea) => array_map('trim', explode('|', $linea, 2)))
            ->filter(fn ($par) => count($par) === 2 && $par[0] !== '')
            ->values()
            ->all();
    }
}
