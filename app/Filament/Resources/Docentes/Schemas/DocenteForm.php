<?php

namespace App\Filament\Resources\Docentes\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocenteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre completo (con título)')
                    ->placeholder('Dr. Juan Pérez García')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('area')
                    ->label('Área / Especialidad'),
                TextInput::make('grado')
                    ->label('Grado académico')
                    ->placeholder('Doctor en Derecho'),
                SpatieMediaLibraryFileUpload::make('foto')
                    ->label('Foto')
                    ->collection('foto')
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->helperText('Menor número aparece primero.'),
                Toggle::make('activo')
                    ->label('Activo')
                    ->default(true)
                    ->helperText('Si está activo, aparece en el sitio público.'),
            ]);
    }
}
