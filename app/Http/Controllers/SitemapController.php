<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $staticRoutes = collect([
            'home', 'presentacion', 'resumen', 'historia', 'mision', 'campo-laboral',
            'objetivos', 'plan-2023', 'plan-2019', 'competencias', 'perfil-ingreso',
            'perfil-egreso', 'revista', 'blog', 'docentes', 'comunicados', 'organigrama', 'documentos',
        ])->filter(fn (string $name) => Route::has($name))->map(fn (string $name) => route($name));

        if (Revista::query()->publica()->exists()) {
            $staticRoutes->push(...collect([
                'revista.actual', 'revista.archivos', 'revista.politicas', 'revista.comite-editorial',
                'revista.comite-cientifico', 'revista.avisos', 'revista.envios', 'revista.sobre',
                'revista.indexacion', 'revista.contacto', 'revista.privacidad', 'revista.preservacion',
                'revista.formatos', 'revista.normas',
            ])->map(fn (string $name) => route($name))->all());
        }

        $urls = $staticRoutes
            ->merge(BlogPost::query()->publicados()->pluck('slug')->map(fn ($slug) => route('blog.show', $slug)))
            ->merge(Comunicado::query()->publicados()->pluck('slug')->map(fn ($slug) => route('comunicados.show', $slug)))
            ->merge(Docente::query()->publicos()->pluck('slug')->map(fn ($slug) => route('docentes.show', $slug)))
            ->merge(RevistaNumero::query()->publicados()->pluck('slug')->map(fn ($slug) => route('revista.numero', $slug)))
            ->merge(Articulo::query()->publicados()->with('numero:id,slug')->get()->map(fn (Articulo $articulo) => route('revista.articulo', [$articulo->numero->slug, $articulo->slug])))
            ->unique()->values();

        return response()->view('sitemap', compact('urls'))->header('Content-Type', 'application/xml');
    }
}
