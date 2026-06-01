<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\BlogService;
use App\Services\EstadisticaService;
use App\Services\RevistaService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Página de inicio con todas sus secciones, alimentada desde el CMS.
     */
    public function index(
        RevistaService $revista,
        BlogService $blog,
        EstadisticaService $estadisticas,
    ): View {
        return view('home', [
            'revista' => $revista->listadoPublico()->take(4),
            'posts' => $blog->listadoPublico()->take(4),
            'matriculados' => $estadisticas->serie('matriculados'),
            // Textos editables del Inicio (con valores por defecto).
            'home' => $this->textosInicio(),
        ]);
    }

    /**
     * Textos del Inicio desde settings, con defaults razonables.
     *
     * @return array<string, string>
     */
    private function textosInicio(): array
    {
        $defaults = [
            'home_hero_titulo' => 'Derecho y Ciencias Políticas',
            'home_hero_subtitulo' => 'Formación jurídica de excelencia con responsabilidad social, al servicio de Áncash y el país desde 1986.',
            'home_hero_cta1' => 'Conoce el programa',
            'home_hero_cta2' => 'Plan de Estudios',
            'home_about_eyebrow' => 'El programa',
            'home_about_titulo' => 'Formando profesionales del derecho desde 1986',
            'home_about_cuerpo' => "El Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM forma abogados con sólida base jurídica, sentido ético y compromiso con el desarrollo de la región Áncash y del país.\n\nNuestro plan de estudios combina la formación teórica con la práctica profesional, la investigación jurídica y la responsabilidad social, preparando a los estudiantes para los desafíos del ejercicio del derecho en el siglo XXI.",
            'home_about_cita' => 'Del esfuerzo de sus hijos, depende el progreso de los pueblos.',
            'home_accesos_eyebrow' => 'Accesos directos',
            'home_accesos_titulo' => 'Explora el programa',
            'home_stats_eyebrow' => 'El programa en cifras',
            'home_stats_titulo' => 'Una comunidad académica en crecimiento',
            'home_stats_narrativa' => 'En 2024 alcanzamos 1049 matriculados y 68 titulados, con más de 40 años formando abogados en 8 áreas del derecho.',
            'home_revista_eyebrow' => 'Revista Jurídica UNASAM',
            'home_revista_titulo' => 'Investigación jurídica original',
            'home_revista_badge' => 'Vol. 1 · Núm. 1 — Marzo 2026',
            'home_blog_eyebrow' => 'Actualidad',
            'home_blog_titulo' => 'Noticias, opiniones y eventos',
        ];

        $guardados = Setting::map();

        $out = [];
        foreach ($defaults as $clave => $default) {
            $valor = $guardados[$clave] ?? null;
            $out[$clave] = ($valor === null || $valor === '') ? $default : $valor;
        }

        return $out;
    }
}
