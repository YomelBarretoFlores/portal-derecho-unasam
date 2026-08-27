<?php

namespace App\Filament\Resources\RevistaContactos\Pages;

use App\Filament\Resources\RevistaContactos\RevistaContactoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevistaContactos extends ListRecords
{
    protected static string $resource = RevistaContactoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
