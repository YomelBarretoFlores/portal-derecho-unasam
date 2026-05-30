<?php

namespace App\Filament\Resources\Estadisticas\Pages;

use App\Filament\Resources\Estadisticas\EstadisticaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEstadisticas extends ListRecords
{
    protected static string $resource = EstadisticaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
