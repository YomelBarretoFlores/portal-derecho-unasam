<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Modelos de contenido cuya edición debe invalidar la caché pública.
     *
     * @var array<int, class-string<Model>>
     */
    private const CONTENT_MODELS = [
        \App\Models\Acceso::class,
        \App\Models\BlogPost::class,
        \App\Models\Articulo::class,
        \App\Models\Docente::class,
        \App\Models\Estadistica::class,
        \App\Models\Comunicado::class,
        \App\Models\Hito::class,
        \App\Models\Objetivo::class,
        \App\Models\Competencia::class,
        \App\Models\AreaLaboral::class,
        \App\Models\PerfilIngresoArea::class,
        \App\Models\Documento::class,
        \App\Models\Curso::class,
        \App\Models\Organigrama::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Comparte los ajustes globales (footer, SEO, contacto, lema) con el layout,
        // el footer y la nav. Setting::map() está cacheado: 0 consultas a Neon por
        // petición salvo cuando la caché está fría.
        View::composer(['layouts.app', 'components.footer', 'components.nav'], function ($view) {
            $view->with('ajustes', $this->ajustesGlobales());
        });

        // Cualquier cambio de contenido en el panel invalida la caché pública:
        // se incrementa la "versión de contenido" que forma parte de las claves
        // de caché de los repositorios (ver BaseRepository::remember).
        $invalidar = static function (): void {
            Cache::add('content.version', 1); // inicializa si falta
            Cache::increment('content.version');
        };

        foreach (self::CONTENT_MODELS as $modelo) {
            $modelo::saved($invalidar);
            $modelo::deleted($invalidar);
        }

        // Las subidas/borrados de archivos (Spatie Media) también invalidan.
        Media::saved($invalidar);
        Media::deleted($invalidar);
    }

    /**
     * Ajustes globales con valores por defecto razonables (desde la caché).
     *
     * @return array<string, string>
     */
    private function ajustesGlobales(): array
    {
        $defaults = [
            'seo_title' => 'Derecho y Ciencias Políticas — UNASAM',
            'seo_description' => 'Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional Santiago Antúnez de Mayolo — Huaraz, Áncash, Perú.',
            'footer_marca' => 'Derecho y Ciencias Políticas',
            'footer_descripcion' => 'Programa de Estudios de la Universidad Nacional Santiago Antúnez de Mayolo.',
            'contacto_direccion' => 'Ciudad Universitaria, Huaraz, Áncash',
            'contacto_telefono' => '(043) 640-020',
            'contacto_email' => 'mesadepartesdigital@unasam.edu.pe',
            'footer_cta_texto' => 'Portal UNASAM',
            'footer_cta_url' => 'https://unasam.edu.pe',
            'lema' => 'Orabunt Causas Melius',
            'home_marquee' => 'Derecho Civil · Derecho Penal · Derecho Constitucional · Derecho Laboral · Derecho Administrativo · Derecho Procesal · Derecho Internacional · Derecho Comercial',
        ];

        try {
            $guardados = Setting::map();
        } catch (\Throwable $e) {
            $guardados = []; // p. ej. antes de migrar
        }

        $resultado = [];
        foreach ($defaults as $clave => $default) {
            $valor = $guardados[$clave] ?? null;
            $resultado[$clave] = ($valor === null || $valor === '') ? $default : $valor;
        }

        return $resultado;
    }
}
