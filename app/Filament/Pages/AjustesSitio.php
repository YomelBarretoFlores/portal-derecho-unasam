<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Página de «Ajustes del sitio»: edita todos los textos sueltos (prosa) del sitio,
 * guardados como pares clave-valor en la tabla settings.
 *
 * Implementada con Livewire plano (propiedades + vista) para no depender de la
 * API de formularios de Filament, que varía entre versiones.
 */
class AjustesSitio extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Páginas institucionales';

    protected static ?string $title = 'Ajustes del sitio';

    protected static ?string $navigationLabel = 'Ajustes del sitio';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.ajustes-sitio';

    // --- Páginas institucionales (Fase 2) ---
    public string $presentacion_titulo = '';
    public string $presentacion_cuerpo = '';
    public string $datos_programa = '';
    public string $mision = '';
    public string $vision = '';
    public string $historia_trayectoria_titulo = '';
    public string $historia_trayectoria_cuerpo = '';
    public string $resumen_titulo = '';
    public string $resumen_cuerpo = '';
    public string $resumen_cita1 = '';
    public string $resumen_cita2 = '';
    public string $resumen_cierre = '';
    public string $perfil_ingreso_especifico = '';
    public string $perfil_egreso_2023 = '';
    public string $perfil_egreso_2019 = '';

    // --- Inicio (Fase 3) ---
    public string $home_hero_titulo = '';
    public string $home_hero_subtitulo = '';
    public string $home_hero_cta1 = '';
    public string $home_hero_cta2 = '';
    public string $home_about_eyebrow = '';
    public string $home_about_titulo = '';
    public string $home_about_cuerpo = '';
    public string $home_about_cita = '';
    public string $home_accesos_eyebrow = '';
    public string $home_accesos_titulo = '';
    public string $home_stats_eyebrow = '';
    public string $home_stats_titulo = '';
    public string $home_stats_narrativa = '';
    public string $home_revista_eyebrow = '';
    public string $home_revista_titulo = '';
    public string $home_revista_badge = '';
    public string $home_blog_eyebrow = '';
    public string $home_blog_titulo = '';
    public string $home_marquee = '';

    // --- Footer / contacto / SEO (Fase 3) ---
    public string $footer_marca = '';
    public string $footer_descripcion = '';
    public string $contacto_direccion = '';
    public string $contacto_telefono = '';
    public string $contacto_email = '';
    public string $footer_cta_texto = '';
    public string $footer_cta_url = '';
    public string $lema = '';
    public string $seo_title = '';
    public string $seo_description = '';

    // --- Plan de Estudios (Fase 3) ---
    public string $plan_grado = '';
    public string $plan_titulo_prof = '';
    public string $plan_modalidad = '';
    public string $plan_pdf_url = '';
    public string $plan_sga_url = '';
    public string $plan_intro = '';

    /**
     * @return array<int, string>
     */
    protected function claves(): array
    {
        return [
            // Fase 2
            'presentacion_titulo', 'presentacion_cuerpo', 'datos_programa',
            'mision', 'vision',
            'historia_trayectoria_titulo', 'historia_trayectoria_cuerpo',
            'resumen_titulo', 'resumen_cuerpo', 'resumen_cita1', 'resumen_cita2', 'resumen_cierre',
            'perfil_ingreso_especifico', 'perfil_egreso_2023', 'perfil_egreso_2019',
            // Inicio
            'home_hero_titulo', 'home_hero_subtitulo', 'home_hero_cta1', 'home_hero_cta2',
            'home_about_eyebrow', 'home_about_titulo', 'home_about_cuerpo', 'home_about_cita',
            'home_accesos_eyebrow', 'home_accesos_titulo',
            'home_stats_eyebrow', 'home_stats_titulo', 'home_stats_narrativa',
            'home_revista_eyebrow', 'home_revista_titulo', 'home_revista_badge',
            'home_blog_eyebrow', 'home_blog_titulo',
            'home_marquee',
            // Footer / SEO
            'footer_marca', 'footer_descripcion', 'contacto_direccion', 'contacto_telefono',
            'contacto_email', 'footer_cta_texto', 'footer_cta_url', 'lema',
            'seo_title', 'seo_description',
            // Plan de Estudios
            'plan_grado', 'plan_titulo_prof', 'plan_modalidad',
            'plan_pdf_url', 'plan_sga_url', 'plan_intro',
        ];
    }

    public function mount(): void
    {
        foreach ($this->claves() as $clave) {
            $this->{$clave} = (string) Setting::get($clave, '');
        }
    }

    public function guardar(): void
    {
        foreach ($this->claves() as $clave) {
            Setting::set($clave, $this->{$clave});
        }

        Notification::make()
            ->title('Ajustes guardados')
            ->success()
            ->send();
    }
}
