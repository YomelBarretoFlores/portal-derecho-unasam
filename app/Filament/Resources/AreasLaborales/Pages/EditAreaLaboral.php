<?php

namespace App\Filament\Resources\AreasLaborales\Pages;

use App\Filament\Resources\AreasLaborales\AreaLaboralResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAreaLaboral extends EditRecord
{
    protected static string $resource = AreaLaboralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
