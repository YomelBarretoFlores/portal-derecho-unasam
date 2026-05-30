<?php

namespace App\Filament\Resources\PerfilIngresoAreas\Pages;

use App\Filament\Resources\PerfilIngresoAreas\PerfilIngresoAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPerfilIngresoAreas extends ListRecords
{
    protected static string $resource = PerfilIngresoAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
