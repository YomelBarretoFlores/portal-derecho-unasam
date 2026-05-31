<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Comparte los ajustes globales (footer, SEO, contacto, lema) con el layout,
        // el footer y la nav, para que se editen desde «Ajustes del sitio» sin
        // repetir Setting::get en cada controlador. Se cachea por request.
        View::composer(['layouts.app', 'components.footer', 'components.nav'], function ($view) {
            static $ajustes = null;

            if ($ajustes === null) {
                $ajustes = $this->cargarAjustesGlobales();
            }

            $view->with('ajustes', $ajustes);
        });
    }

    /**
     * Carga los settings globales con valores por defecto razonables.
     *
     * @return array<string, string>
     */
    private function cargarAjustesGlobales(): array
    {
        $claves = [
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
        ];

        // Una sola consulta a la tabla settings; cae al valor por defecto si falta.
        try {
            $guardados = Setting::query()->pluck('valor', 'clave')->all();
        } catch (\Throwable $e) {
            $guardados = []; // p. ej. antes de migrar
        }

        $resultado = [];
        foreach ($claves as $clave => $default) {
            $valor = $guardados[$clave] ?? null;
            $resultado[$clave] = ($valor === null || $valor === '') ? $default : $valor;
        }

        return $resultado;
    }
}
