<?php

namespace App\Filament\Resources\Articulos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
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
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('descargas')
                    ->label('Descargas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('publicado')
                    ->label('Publicado')
                    ->boolean(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                TernaryFilter::make('publicado')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Publicados')
                    ->falseLabel('Borradores'),
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
