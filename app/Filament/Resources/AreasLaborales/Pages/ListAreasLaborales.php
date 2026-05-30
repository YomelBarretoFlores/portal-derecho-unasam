<?php

namespace App\Filament\Resources\AreasLaborales\Pages;

use App\Filament\Resources\AreasLaborales\AreaLaboralResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAreasLaborales extends ListRecords
{
    protected static string $resource = AreaLaboralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
