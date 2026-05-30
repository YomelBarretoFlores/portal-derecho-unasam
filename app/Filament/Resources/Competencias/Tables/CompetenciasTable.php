<?php

namespace App\Filament\Resources\Competencias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompetenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                TextColumn::make('grupo')->label('Grupo')->badge()->sortable(),
                TextColumn::make('plan')->label('Plan')->sortable(),
                TextColumn::make('nombre')->label('Nombre')->limit(30)->toggleable(),
                IconColumn::make('vigente')->label('Vigente')->boolean(),
                TextColumn::make('texto')->label('Descripción')->limit(50)->searchable(),
            ])
            ->defaultSort('orden')
            ->filters([
                SelectFilter::make('grupo')->options([
                    'Generales' => 'Generales',
                    'Específicas' => 'Específicas',
                ]),
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
