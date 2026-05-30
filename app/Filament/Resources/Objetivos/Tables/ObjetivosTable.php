<?php

namespace App\Filament\Resources\Objetivos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ObjetivosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                TextColumn::make('plan')->label('Plan')->badge()->sortable(),
                IconColumn::make('vigente')->label('Vigente')->boolean(),
                TextColumn::make('texto')->label('Objetivo')->limit(70)->searchable(),
            ])
            ->defaultSort('orden')
            ->filters([
                SelectFilter::make('plan')->options([
                    'Plan de Estudios 2023' => 'Plan de Estudios 2023',
                    'Plan de Estudios 2019' => 'Plan de Estudios 2019',
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
