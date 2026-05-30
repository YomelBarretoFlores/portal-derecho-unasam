<?php

namespace App\Filament\Resources\Documentos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                TextColumn::make('titulo')->label('Título')->searchable()->limit(50),
                TextColumn::make('categoria')->label('Categoría')->badge()->sortable(),
                TextColumn::make('fecha')->label('Fecha')->date('d/m/Y')->sortable(),
            ])
            ->defaultSort('orden')
            ->filters([
                SelectFilter::make('categoria')->options([
                    'Planes de Estudio' => 'Planes de Estudio',
                    'Reglamentos' => 'Reglamentos',
                    'Resoluciones' => 'Resoluciones',
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
