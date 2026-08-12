<?php

namespace App\Filament\Resources\Comunicados\Tables;

use App\Enums\EditorialStatus;
use App\Filament\Actions\PreviewActions;
use App\Filament\Tables\EditorialStatusColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                EditorialStatusColumn::make(),
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
