<?php

namespace App\Filament\Resources\Articulos\Tables;

use App\Enums\EditorialStatus;
use App\Filament\Actions\PreviewActions;
use App\Filament\Tables\EditorialStatusColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticulosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('numero.titulo')
                    ->label('Número')
                    ->placeholder('Sin asignar')
                    ->toggleable(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('descargas')
                    ->label('Descargas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                EditorialStatusColumn::make(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('estado_editorial')->label('Estado')->options(EditorialStatus::options()),
            ])
            ->recordActions([
                PreviewActions::preview(),
                PreviewActions::published(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
