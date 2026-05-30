<?php

namespace App\Filament\Resources\Comunicados\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ComunicadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Se genera automáticamente desde el título.'),
                SpatieMediaLibraryFileUpload::make('imagen')
                    ->label('Imagen destacada')
                    ->collection('imagen')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),
                Textarea::make('resumen')
                    ->label('Resumen')
                    ->rows(2)
                    ->maxLength(300)
                    ->columnSpanFull(),
                RichEditor::make('contenido')
                    ->label('Contenido')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('publicado')
                    ->label('Publicado')
                    ->helperText('Si está activo, aparece en el sitio público.'),
                DateTimePicker::make('fecha_publicacion')
                    ->label('Fecha de publicación')
                    ->default(now()),
            ]);
    }
}
