<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Enums\EditorialStatus;
use App\Filament\Actions\PreviewActions;
use App\Filament\Tables\EditorialStatusColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('autor')
                    ->label('Autor')
                    ->toggleable(),
                EditorialStatusColumn::make(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'noticia' => 'Noticia',
                        'opinion' => 'Opinión',
                        'evento' => 'Evento',
                    ]),
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
