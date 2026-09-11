<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\PublicContentCache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Página de inicio con todas sus secciones, alimentada desde el CMS.
     */
    public function index(PublicContentCache $content): View
    {
        return view('home', [
            ...$content->home(),
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
            'home_hero_stat1_label' => 'Años de trayectoria',
            'home_hero_stat1_valor' => '40',
            'home_hero_stat1_sufijo' => '+',
            'home_hero_stat2_label' => 'Áreas del Derecho',
            'home_hero_stat2_valor' => '8',
            'home_hero_stat2_sufijo' => '',
            // Fotografía de fondo del hero. Vacío = la que viaja con la
            // aplicación, servida en dos anchos y con respaldo JPEG. Al poner
            // una dirección se usa esa, tal cual: de una imagen externa no
            // podemos generar las versiones optimizadas.
            'home_hero_foto_url' => '',

            // Mascota de la facultad.
            //
            // El valor por defecto queda VACÍO a propósito, aunque el archivo
            // viaje con la aplicación. Si aquí pusiera la ruta, vaciar el campo
            // en el panel no serviría de nada: el valor por defecto volvería a
            // aparecer, y el texto de ayuda —«déjelo vacío para no mostrarla»—
            // sería mentira. La ruta se carga una sola vez en la base, con la
            // migración 000015, y a partir de ahí manda el panel.
            'home_hero_mascota_url' => '',
            'home_hero_mascota_alt' => '',
            'home_about_eyebrow' => 'El programa',
            'home_about_titulo' => 'Formando profesionales del derecho desde 1986',
            'home_about_cuerpo' => "El Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM forma abogados con sólida base jurídica, sentido ético y compromiso con el desarrollo de la región Áncash y del país.\n\nNuestro plan de estudios combina la formación teórica con la práctica profesional, la investigación jurídica y la responsabilidad social, preparando a los estudiantes para los desafíos del ejercicio del derecho en el siglo XXI.",
            'home_about_cita' => 'Del esfuerzo de sus hijos, depende el progreso de los pueblos.',
            'home_accesos_eyebrow' => 'Accesos directos',
            'home_accesos_titulo' => 'Explora el programa',
            'home_stats_eyebrow' => 'El programa en cifras',
            'home_stats_titulo' => 'Una comunidad académica en crecimiento',
            'home_stats_narrativa' => 'En 2024 alcanzamos 1049 matriculados y 68 titulados, con más de 40 años formando abogados en 8 áreas del derecho.',
            'home_revista_eyebrow' => 'Revista Derecho y Cultura',
            'home_revista_titulo' => 'Investigación jurídica original',
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
