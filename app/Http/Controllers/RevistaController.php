<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaAviso;
use App\Models\RevistaDocumento;
use App\Models\RevistaNumero;
use App\Services\PublicContentCache;
use App\Services\RevistaService;
use App\Services\RevistaSubmissionService;
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

    public function actual(RevistaService $revista): View
    {
        $ficha = $this->ficha($revista);
        $numero = RevistaNumero::query()->publicados()->where('revista_id', $ficha->id)->where('es_actual', true)->with(['media', 'articulos'])->first();

        return view('revista.actual', ['revista' => $ficha, 'numero' => $numero]);
    }

    public function archivos(Request $request, RevistaService $revista): View
    {
        $ficha = $this->ficha($revista);
        $numeros = RevistaNumero::query()->publicados()->where('revista_id', $ficha->id)->where('es_actual', false)->with('media')
            ->orderByDesc('fecha_publicacion')->orderByDesc('orden')->paginate(12)->withQueryString();

        return view('revista.archivos', compact('ficha', 'numeros'));
    }

    public function politicas(RevistaService $revista): View
    {
        return $this->contentPage($revista, 'Políticas editoriales', 'contenido_politicas');
    }

    public function sobre(RevistaService $revista): View
    {
        return $this->contentPage($revista, 'Sobre la revista', 'contenido_sobre');
    }

    public function indexacion(RevistaService $revista): View
    {
        return $this->contentPage($revista, 'Indexación', 'contenido_indexacion', 'Proceso de indexación en curso.');
    }

    public function privacidad(RevistaService $revista): View
    {
        return $this->contentPage($revista, 'Declaración de privacidad', 'contenido_privacidad', 'Contenido institucional en preparación.');
    }

    public function preservacion(RevistaService $revista): View
    {
        return $this->contentPage($revista, 'Preservación digital', 'contenido_preservacion', 'Contenido institucional en preparación.');
    }

    public function comiteEditorial(RevistaService $revista): View
    {
        $ficha = $this->ficha($revista);
        $groups = ['director_fundador', 'editores', 'comite_editorial', 'correctores_estilo', 'asistentes_editoriales'];

        return view('revista.comite', ['revista' => $ficha, 'title' => 'Comité editorial', 'grupos' => $ficha->miembros->whereIn('grupo', $groups)->groupBy('grupo')]);
    }

    public function comiteCientifico(RevistaService $revista): View
    {
        $ficha = $this->ficha($revista);

        return view('revista.comite', ['revista' => $ficha, 'title' => 'Comité científico', 'grupos' => $ficha->miembros->whereIn('grupo', ['consejo_cientifico', 'consejo_revisores'])->groupBy('grupo')]);
    }

    public function avisos(RevistaService $revista): View
    {
        $ficha = $this->ficha($revista);
        $avisos = RevistaAviso::query()->publicados()->where('revista_id', $ficha->id)->with('media')->latest('fecha_publicacion')->paginate(10);

        return view('revista.avisos', compact('ficha', 'avisos'));
    }

    public function formatos(RevistaService $revista): View
    {
        $ficha = $this->ficha($revista);
        $documentos = RevistaDocumento::query()->publicos()->where('revista_id', $ficha->id)->where('categoria', 'formato')->orderBy('orden')->get();

        return view('revista.formatos', compact('ficha', 'documentos'));
    }

    public function contacto(RevistaService $revista): View
    {
        $ficha = $this->ficha($revista);

        return view('revista.contacto', ['revista' => $ficha, 'contactos' => $ficha->contactos()->publicos()->get()]);
    }

    public function envios(RevistaService $revista, RevistaSubmissionService $submissions): View
    {
        $ficha = $this->ficha($revista);

        return view('revista.envios', ['revista' => $ficha, 'lineas' => $ficha->lineasInvestigacion()->activas()->get(), 'submissionsEnabled' => $submissions->available()]);
    }

    public function normas(RevistaService $revista): View
    {
        $ficha = $revista->revistaPublica();
        abort_unless($ficha && filled($ficha->normas_publicacion), 404);

        $documento = $ficha->documentos()->publicos()->where('categoria', 'norma')->orderBy('orden')->first();

        return view('revista.normas', ['revista' => $ficha, 'documento' => $documento]);
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

    private function ficha(RevistaService $service): Revista
    {
        $ficha = $service->revistaPublica();
        abort_unless($ficha, 404);

        return $ficha;
    }

    private function contentPage(RevistaService $service, string $title, string $field, ?string $fallback = null): View
    {
        $revista = $this->ficha($service);
        $content = $revista->{$field} ?: '<p>'.e($fallback ?: 'Contenido institucional en preparación.').'</p>';

        return view('revista.page', compact('revista', 'title', 'content'));
    }
}
