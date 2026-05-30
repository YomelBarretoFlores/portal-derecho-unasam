<?php

namespace App\Filament\Resources\Estadisticas\Pages;

use App\Filament\Resources\Estadisticas\EstadisticaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEstadistica extends EditRecord
{
    protected static string $resource = EstadisticaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
