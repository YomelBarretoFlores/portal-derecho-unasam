<?php

namespace App\Filament\Resources\RevistaDocumentos\Pages;

use App\Filament\Resources\RevistaDocumentos\RevistaDocumentoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevistaDocumentos extends ListRecords
{
    protected static string $resource = RevistaDocumentoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
