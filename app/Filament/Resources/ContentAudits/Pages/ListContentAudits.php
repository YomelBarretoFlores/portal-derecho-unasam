<?php

namespace App\Filament\Resources\ContentAudits\Pages;

use App\Filament\Resources\ContentAudits\ContentAuditResource;
use Filament\Resources\Pages\ListRecords;

class ListContentAudits extends ListRecords
{
    protected static string $resource = ContentAuditResource::class;
}
