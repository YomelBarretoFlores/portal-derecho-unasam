<?php

namespace App\Filament\Resources\Organigramas\Pages;

use App\Filament\Resources\Organigramas\OrganigramaResource;
use App\Models\Organigrama;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListOrganigramas extends ListRecords
{
    protected static string $resource = OrganigramaResource::class;

    /**
     * Singleton: no hay acción de crear porque la fila única se siembra. Pero si
     * llegara a borrarse no habría forma de recrearla desde el panel, así que se
     * ofrece una restauración explícita, visible solo cuando falta.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('restaurar')
                ->label('Restaurar ficha del organigrama')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => ! Organigrama::query()->exists())
                ->action(function (): void {
                    Organigrama::singleton();
                    Notification::make()->title('Ficha del organigrama restaurada')->success()->send();
                }),
        ];
    }
}
