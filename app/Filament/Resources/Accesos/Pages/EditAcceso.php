<?php

namespace App\Filament\Resources\Accesos\Pages;

use App\Filament\Resources\Accesos\AccesoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcceso extends EditRecord
{
    protected static string $resource = AccesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
