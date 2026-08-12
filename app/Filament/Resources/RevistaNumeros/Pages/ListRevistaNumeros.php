<?php

namespace App\Filament\Resources\RevistaNumeros\Pages;

use App\Filament\Resources\RevistaNumeros\RevistaNumeroResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevistaNumeros extends ListRecords
{
    protected static string $resource = RevistaNumeroResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
