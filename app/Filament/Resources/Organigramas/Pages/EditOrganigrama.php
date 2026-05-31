<?php

namespace App\Filament\Resources\Organigramas\Pages;

use App\Filament\Resources\Organigramas\OrganigramaResource;
use Filament\Resources\Pages\EditRecord;

class EditOrganigrama extends EditRecord
{
    protected static string $resource = OrganigramaResource::class;

    // Singleton: sin botón de borrar.
    protected function getHeaderActions(): array
    {
        return [];
    }
}
