<?php

namespace App\Filament\Resources\RevistaLineasInvestigacion\Pages;

use App\Filament\Resources\RevistaLineasInvestigacion\RevistaLineaInvestigacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevistaLineasInvestigacion extends ListRecords
{
    protected static string $resource = RevistaLineaInvestigacionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
