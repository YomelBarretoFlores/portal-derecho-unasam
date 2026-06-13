<?php

namespace App\Filament\Resources\Accesos\Pages;

use App\Filament\Resources\Accesos\AccesoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccesos extends ListRecords
{
    protected static string $resource = AccesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
