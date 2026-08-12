<?php

namespace App\Filament\Resources\RevistaMiembros\Pages;

use App\Filament\Resources\RevistaMiembros\RevistaMiembroResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevistaMiembros extends ListRecords
{
    protected static string $resource = RevistaMiembroResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
