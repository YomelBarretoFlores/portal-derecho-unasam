<?php

namespace App\Filament\Resources\Organigramas\Pages;

use App\Filament\Resources\Organigramas\OrganigramaResource;
use Filament\Resources\Pages\ListRecords;

class ListOrganigramas extends ListRecords
{
    protected static string $resource = OrganigramaResource::class;

    // Singleton: sin acción de crear (la fila única se siembra).
    protected function getHeaderActions(): array
    {
        return [];
    }
}
