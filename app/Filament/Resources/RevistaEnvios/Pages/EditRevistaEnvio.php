<?php

namespace App\Filament\Resources\RevistaEnvios\Pages;

use App\Filament\Resources\RevistaEnvios\RevistaEnvioResource;
use App\Models\RevistaEnvio;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditRevistaEnvio extends EditRecord
{
    protected static string $resource = RevistaEnvioResource::class;

    /**
     * Filament rellena el formulario desde attributesToArray(), que respeta el
     * $hidden del modelo. Como RevistaEnvio oculta el documento y el WhatsApp
     * —para que no se filtren en notificaciones ni serializaciones—, esos campos
     * se renderizaban vacíos y el equipo editorial no tenía dónde consultar los
     * datos de contacto que el propio formulario público exige como obligatorios.
     *
     * Lo mismo ocurría con la línea de investigación, que viene de una relación,
     * y con el tipo de contribución, que mostraba la clave interna en lugar de su
     * etiqueta. Todos estos campos son de solo lectura (dehydrated(false)), así
     * que rellenarlos aquí no altera nada al guardar.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['documento_identidad'] = $this->record->documento_identidad;
        $data['whatsapp'] = $this->record->whatsapp;
        $data['lineaInvestigacion']['nombre'] = $this->record->lineaInvestigacion?->nombre;
        $data['tipo_contribucion'] = RevistaEnvio::TIPOS[$this->record->tipo_contribucion]
            ?? $this->record->tipo_contribucion;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        $actions = [Action::make('correo')->label('Responder por correo')->icon('heroicon-o-envelope')->url(fn (): string => 'mailto:'.$this->record->email_institucional.'?subject='.rawurlencode('Revista Derecho y Cultura · '.$this->record->codigo_seguimiento).'&body='.rawurlencode("Estimado/a {$this->record->nombres}:\n\nRespecto de su envío {$this->record->codigo_seguimiento}:\n\n"))];
        foreach (['manuscrito' => 'Manuscrito', 'carta' => 'Carta', 'declaracion' => 'Declaración', 'constancia' => 'Constancia'] as $type => $label) {
            $actions[] = Action::make('descargar_'.$type)->label($label)->icon('heroicon-o-arrow-down-tray')->url(fn (): string => route('revista.envios.admin.download', [$this->record, $type]))->openUrlInNewTab();
        }
        foreach ($this->record->versiones as $version) {
            $actions[] = Action::make('version_'.$version->numero)->label('Corrección '.$version->numero)->icon('heroicon-o-document-arrow-down')->url(fn () => route('revista.envios.admin.version', [$this->record, $version]))->openUrlInNewTab();
        }

        return $actions;
    }
}
