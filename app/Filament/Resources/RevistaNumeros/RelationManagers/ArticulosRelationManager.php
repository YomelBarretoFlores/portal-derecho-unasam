<?php

namespace App\Filament\Resources\RevistaNumeros\RelationManagers;

use App\Enums\EditorialStatus;
use App\Filament\Resources\Articulos\Schemas\ArticuloForm;
use App\Filament\Tables\EditorialStatusColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Artículos del número, gestionados desde la ficha del propio número para que
 * el editor no tenga que ir al listado general y filtrar a mano.
 */
class ArticulosRelationManager extends RelationManager
{
    protected static string $relationship = 'articulos';

    protected static ?string $title = 'Artículos del número';

    protected static ?string $modelLabel = 'artículo';

    protected static ?string $pluralModelLabel = 'artículos';

    public function form(Schema $schema): Schema
    {
        return ArticuloForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                TextColumn::make('orden')->label('Orden')->sortable(),
                TextColumn::make('titulo')->label('Título')->searchable()->limit(50)->wrap(),
                TextColumn::make('categoria')->label('Categoría')->badge()->toggleable(),
                TextColumn::make('fecha')->label('Fecha')->date('d/m/Y')->sortable(),
                EditorialStatusColumn::make(),
            ])
            ->defaultSort('orden')
            ->filters([
                SelectFilter::make('estado_editorial')->label('Estado')->options(EditorialStatus::options()),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
