<?php

namespace App\Filament\Resources\Accesos\Schemas;

use App\Rules\SafeUrl;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccesoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->label('Enlace')
                    ->required()
                    ->rule(new SafeUrl)
                    ->helperText('Ruta interna del sitio (p. ej. /plan-2023) o una URL completa (https://…).')
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
                Toggle::make('activo')
                    ->label('Visible en el sitio')
                    ->default(true),
            ]);
    }
}
