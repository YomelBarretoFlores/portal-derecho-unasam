<?php

namespace App\Filament\Resources\RevistaNumeros\Pages;

use App\Filament\Actions\PreviewActions;
use App\Filament\Resources\RevistaNumeros\RevistaNumeroResource;
use Filament\Resources\Pages\EditRecord;

class EditRevistaNumero extends EditRecord
{
    protected static string $resource = RevistaNumeroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewActions::preview(),
            PreviewActions::published(),
        ];
    }
}
