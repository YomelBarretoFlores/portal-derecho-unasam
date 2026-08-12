<?php

namespace App\Filament\Tables;

use App\Enums\EditorialStatus;
use Filament\Tables\Columns\TextColumn;

class EditorialStatusColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('estado_editorial')
            ->label('Estado')
            ->badge()
            ->formatStateUsing(fn (?string $state): string => EditorialStatus::options()[$state] ?? 'Borrador')
            ->color(fn (?string $state): string => EditorialStatus::color($state));
    }
}
