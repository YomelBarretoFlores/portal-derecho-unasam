<?php

namespace App\Filament\Resources\PerfilIngresoAreas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PerfilIngresoAreasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                TextColumn::make('titulo')->label('Área')->searchable(),
                TextColumn::make('items')
                    ->label('Competencias')
                    ->badge()
                    ->limitList(2),
            ])
            ->defaultSort('orden')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
