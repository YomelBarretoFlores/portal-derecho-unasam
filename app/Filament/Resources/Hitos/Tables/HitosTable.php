<?php

namespace App\Filament\Resources\Hitos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HitosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                TextColumn::make('anio')->label('Año')->sortable(),
                TextColumn::make('titulo')->label('Título')->searchable()->limit(60),
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
