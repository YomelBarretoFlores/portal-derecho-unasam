<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PreviewController extends Controller
{
    public function blog(BlogPost $post): Response
    {
        Gate::authorize('view', $post);
        $post->loadMissing('media');

        return $this->render('blog.show', compact('post'));
    }

    public function comunicado(Comunicado $comunicado): Response
    {
        Gate::authorize('view', $comunicado);
        $comunicado->loadMissing('media');

        return $this->render('comunicados.show', compact('comunicado'));
    }

    public function docente(Docente $docente): Response
    {
        Gate::authorize('view', $docente);
        $docente->loadMissing('media');

        return $this->render('docentes.show', compact('docente'));
    }

    public function revista(Request $request, Revista $revista): Response
    {
        Gate::authorize('view', $revista);
        $revista->loadMissing(['media', 'miembros' => fn ($query) => $query->ordenados()]);
        $q = trim($request->string('q')->toString());
        $numeros = $revista->numeros()->with('media')
            ->when($q, fn ($query) => $query->where('titulo', 'like', "%{$q}%"))
            ->orderByDesc('fecha_publicacion')->paginate(12)->withQueryString();

        // La portada pública encabeza con el número en curso; la vista previa
        // tiene que enseñar lo mismo, incluidos los números aún sin publicar.
        $actual = $revista->numeros()->with(['media', 'articulos' => fn ($query) => $query->orderBy('orden')])
            ->orderByDesc('es_actual')->orderByDesc('fecha_publicacion')->orderByDesc('orden')
            ->first();

        return $this->render('revista.index', compact('revista', 'numeros', 'q', 'actual'));
    }

    public function numero(Request $request, RevistaNumero $numero): Response
    {
        Gate::authorize('view', $numero);
        $numero->loadMissing(['revista', 'media']);
        $categoria = trim($request->string('categoria')->toString());
        $q = trim($request->string('q')->toString());
        $articulos = $numero->articulos()->with('media')
            ->when($categoria, fn ($query) => $query->where('categoria', $categoria))
            ->when($q, fn ($query) => $query->where(fn ($search) => $search
                ->where('titulo', 'like', "%{$q}%")
                ->orWhere('resumen', 'like', "%{$q}%")))
            ->paginate(12)->withQueryString();
        $categorias = $numero->articulos()->whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');

        return $this->render('revista.numero', compact('numero', 'articulos', 'categorias', 'categoria', 'q'));
    }

    public function articulo(Articulo $articulo): Response
    {
        Gate::authorize('view', $articulo);
        abort_if(blank($articulo->revista_numero_id), 422, 'Asigna el artículo a un número antes de previsualizarlo.');
        $articulo->loadMissing(['numero.revista', 'media']);
        $numero = $articulo->numero;

        return $this->render('revista.articulo', compact('numero', 'articulo'));
    }

    /** @param array<string, mixed> $data */
    private function render(string $view, array $data): Response
    {
        return response()
            ->view($view, [...$data, 'previewMode' => true])
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
