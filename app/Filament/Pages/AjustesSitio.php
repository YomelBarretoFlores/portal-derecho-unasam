<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Página de «Ajustes del sitio»: edita los textos sueltos (prosa) de las
 * páginas institucionales, guardados como pares clave-valor en la tabla settings.
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

    // --- Campos (una propiedad por clave de settings) ---
    public string $presentacion_titulo = '';
    public string $presentacion_cuerpo = '';
    public string $datos_programa = '';
    public string $mision = '';
    public string $vision = '';
    public string $historia_trayectoria_titulo = '';
    public string $historia_trayectoria_cuerpo = '';
    public string $resumen_cuerpo = '';
    public string $resumen_cita1 = '';
    public string $resumen_cita2 = '';
    public string $resumen_cierre = '';
    public string $perfil_ingreso_especifico = '';
    public string $perfil_egreso_2023 = '';
    public string $perfil_egreso_2019 = '';

    /**
     * @return array<int, string>
     */
    protected function claves(): array
    {
        return [
            'presentacion_titulo', 'presentacion_cuerpo', 'datos_programa',
            'mision', 'vision',
            'historia_trayectoria_titulo', 'historia_trayectoria_cuerpo',
            'resumen_cuerpo', 'resumen_cita1', 'resumen_cita2', 'resumen_cierre',
            'perfil_ingreso_especifico', 'perfil_egreso_2023', 'perfil_egreso_2019',
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
