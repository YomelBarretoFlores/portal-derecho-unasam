<?php

namespace App\Filament\Resources\RevistaAvisos\Pages;

use App\Filament\Resources\RevistaAvisos\RevistaAvisoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevistaAvisos extends ListRecords
{
    protected static string $resource = RevistaAvisoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
