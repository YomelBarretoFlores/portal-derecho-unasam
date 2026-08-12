<?php

namespace App\Filament\Resources\Docentes\Tables;

use App\Enums\EditorialStatus;
use App\Filament\Actions\PreviewActions;
use App\Filament\Tables\EditorialStatusColumn;
use App\Models\Docente;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocentesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('foto')
                    ->label('Foto')
                    ->view('filament.tables.columns.docente-avatar'),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->description(fn (Docente $record): ?string => $record->grado)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->searchable()
                    ->toggleable(),
                EditorialStatusColumn::make(),
                TextColumn::make('orden')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->defaultSort('orden')
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
