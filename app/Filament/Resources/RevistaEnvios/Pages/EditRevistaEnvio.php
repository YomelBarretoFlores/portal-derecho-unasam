<?php

namespace App\Filament\Resources\RevistaEnvios\Pages;

use App\Filament\Resources\RevistaEnvios\RevistaEnvioResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditRevistaEnvio extends EditRecord
{
    protected static string $resource = RevistaEnvioResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [Action::make('correo')->label('Responder por correo')->icon('heroicon-o-envelope')->url(fn (): string => 'mailto:'.$this->record->email_institucional.'?subject='.rawurlencode('Revista Derecho y Cultura · '.$this->record->codigo_seguimiento).'&body='.rawurlencode("Estimado/a {$this->record->nombres}:\n\nRespecto de su envío {$this->record->codigo_seguimiento}:\n\n"))];
        foreach (['manuscrito' => 'Manuscrito', 'carta' => 'Carta', 'declaracion' => 'Declaración', 'constancia' => 'Constancia'] as $type => $label) {
            $actions[] = Action::make('descargar_'.$type)->label($label)->icon('heroicon-o-arrow-down-tray')->url(fn (): string => route('revista.envios.admin.download', [$this->record, $type]));
        }
        foreach ($this->record->versiones as $version) {
            $actions[] = Action::make('version_'.$version->numero)->label('Corrección '.$version->numero)->icon('heroicon-o-document-arrow-down')->url(fn () => route('revista.envios.admin.version', [$this->record, $version]));
        }

        return $actions;
    }
}
