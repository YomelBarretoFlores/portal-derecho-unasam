<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\PublicContentCache;
use Illuminate\Http\Request;
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
            'titulo' => Setting::get('resumen_titulo'),
            'cuerpo' => Setting::get('resumen_cuerpo'),
            'cita1' => Setting::get('resumen_cita1'),
            'cita2' => Setting::get('resumen_cita2'),
            'cierre' => Setting::get('resumen_cierre'),
        ]);
    }

    public function historia(PublicContentCache $content): View
    {
        return view('pages.historia', [
            'hitos' => $content->hitos(),
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

    public function campoLaboral(PublicContentCache $content): View
    {
        return view('pages.campo-laboral', [
            'areas' => $content->areasLaborales(),
        ]);
    }

    public function objetivos(PublicContentCache $content): View
    {
        return view('pages.objetivos', [
            'planes' => $content->objetivos(),
        ]);
    }

    public function planEstudios2023(PublicContentCache $content): View
    {
        return view('pages.plan-2023', [
            'grado' => Setting::get('plan_grado', 'Bachiller en Derecho'),
            'tituloProf' => Setting::get('plan_titulo_prof', 'Abogado(a)'),
            'modalidad' => Setting::get('plan_modalidad', 'Presencial'),
            'pdfUrl' => Setting::get('plan_pdf_url'),
            'sgaUrl' => Setting::get('plan_sga_url'),
            'intro' => Setting::get('plan_intro'),
            'ciclos' => $content->cursos('2023'),
            'plan' => '2023',
        ]);
    }

    public function planEstudios2019(PublicContentCache $content): View
    {
        return view('pages.plan-2023', [
            'grado' => Setting::get('plan_grado', 'Bachiller en Derecho'),
            'tituloProf' => Setting::get('plan_titulo_prof', 'Abogado(a)'),
            'modalidad' => Setting::get('plan_modalidad', 'Presencial'),
            'pdfUrl' => null,
            'sgaUrl' => null,
            'intro' => null,
            'ciclos' => $content->cursos('2019'),
            'plan' => '2019',
        ]);
    }

    public function competencias(PublicContentCache $content): View
    {
        return view('pages.competencias', [
            'grupos' => $content->competencias(),
        ]);
    }

    public function perfilIngreso(PublicContentCache $content): View
    {
        return view('pages.perfil-ingreso', [
            'areas' => $content->perfilIngreso(),
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

    public function organigrama(PublicContentCache $content): View
    {
        return view('pages.organigrama', $content->organigrama());
    }

    public function documentos(Request $request, PublicContentCache $content): View
    {
        $q = trim($request->string('q')->toString());
        $categoria = trim($request->string('categoria')->toString());

        return view('pages.documentos', [
            ...$content->documentos($q, $categoria, $request->integer('page', 1)),
            'q' => $q,
            'categoria' => $categoria,
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

        return collect(preg_split('/\R/', trim($raw)) ?: [])
            ->map(fn ($linea) => array_map('trim', explode('|', $linea, 2)))
            ->filter(fn ($par) => count($par) === 2 && $par[0] !== '')
            ->values()
            ->all();
    }
}
