<?php

namespace App\Filament\Resources\Estadisticas\Tables;

use App\Models\Estadistica;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EstadisticasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Indicador')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Estadistica::TIPOS[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('anio')
                    ->label('Año')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('anio')
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Indicador')
                    ->options(Estadistica::TIPOS),
            ])
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
