<?php

namespace App\Filament\Resources\PerfilIngresoAreas\Pages;

use App\Filament\Resources\PerfilIngresoAreas\PerfilIngresoAreaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPerfilIngresoArea extends EditRecord
{
    protected static string $resource = PerfilIngresoAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
