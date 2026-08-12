<?php

namespace App\Filament\Resources\Hitos\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HitoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('anio')
                    ->label('Año')
                    ->numeric()
                    ->required(),
                TextInput::make('titulo')
                    ->label('Título')
                    ->required(),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
