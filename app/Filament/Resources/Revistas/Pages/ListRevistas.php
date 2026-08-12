<?php

namespace App\Filament\Resources\Revistas\Pages;

use App\Filament\Resources\Revistas\RevistaResource;
use App\Models\Revista;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevistas extends ListRecords
{
    protected static string $resource = RevistaResource::class;

    protected function getHeaderActions(): array
    {
        return Revista::query()->exists() ? [] : [CreateAction::make()];
    }
}
