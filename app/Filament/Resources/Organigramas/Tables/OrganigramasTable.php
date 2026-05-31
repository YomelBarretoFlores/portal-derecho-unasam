<?php

namespace App\Filament\Resources\Organigramas\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganigramasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('imagen')
                    ->label('Imagen')
                    ->collection('imagen'),
                TextColumn::make('titulo')->label('Título'),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime('d/m/Y H:i'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
