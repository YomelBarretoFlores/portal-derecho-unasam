<?php

namespace App\Filament\Resources\Revistas\Pages;

use App\Filament\Actions\PreviewActions;
use App\Filament\Resources\Revistas\RevistaResource;
use Filament\Resources\Pages\EditRecord;

class EditRevista extends EditRecord
{
    protected static string $resource = RevistaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewActions::preview(),
            PreviewActions::published(),
        ];
    }
}
