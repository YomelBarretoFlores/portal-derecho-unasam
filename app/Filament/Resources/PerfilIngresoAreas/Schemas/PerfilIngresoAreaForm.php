<?php

namespace App\Filament\Resources\PerfilIngresoAreas\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PerfilIngresoAreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Área')
                    ->placeholder('Ciencias Sociales')
                    ->required()
                    ->columnSpanFull(),
                TagsInput::make('items')
                    ->label('Competencias / viñetas')
                    ->placeholder('Añadir competencia')
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
