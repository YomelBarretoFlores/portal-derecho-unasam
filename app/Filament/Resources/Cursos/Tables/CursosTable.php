<?php

namespace App\Filament\Resources\Cursos\Tables;

use App\Enums\EditorialStatus;
use App\Filament\Tables\EditorialStatusColumn;
use App\Models\Curso;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan')->label('Plan')->badge()->sortable(),
                TextColumn::make('ciclo')->label('Ciclo')->sortable(),
                TextColumn::make('nombre')->label('Curso')->searchable()->limit(50),
                TextColumn::make('creditos')->label('Créditos')->sortable(),
                TextColumn::make('tipo')->label('Tipo')->badge()->toggleable(),
                TextColumn::make('orden')->label('Orden')->sortable()->toggleable(isToggledHiddenByDefault: true),
                EditorialStatusColumn::make(),
            ])
            ->defaultSort('ciclo')
            ->filters([
                SelectFilter::make('plan')->options(['2023' => 'Plan 2023', '2019' => 'Plan 2019']),
                SelectFilter::make('ciclo')
                    ->options(array_combine(range(1, 10), range(1, 10))),
                SelectFilter::make('tipo')
                    ->options(Curso::TIPOS),
                SelectFilter::make('estado_editorial')->label('Estado')->options(EditorialStatus::options()),
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
