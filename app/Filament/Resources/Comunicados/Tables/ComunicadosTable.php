<?php

namespace App\Filament\Resources\Comunicados\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ComunicadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('imagen')
                    ->label('Imagen')
                    ->collection('imagen'),
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                IconColumn::make('publicado')
                    ->label('Publicado')
                    ->boolean(),
                TextColumn::make('fecha_publicacion')
                    ->label('Publicación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_publicacion', 'desc')
            ->filters([
                TernaryFilter::make('publicado')
                    ->label('Estado de publicación')
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
