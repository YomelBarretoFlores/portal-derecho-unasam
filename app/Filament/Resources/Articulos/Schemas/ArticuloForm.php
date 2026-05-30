<?php

namespace App\Filament\Resources\Articulos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticuloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->helperText('Se genera automáticamente desde el título.'),
                TagsInput::make('autores')
                    ->label('Autores')
                    ->placeholder('Añadir autor')
                    ->columnSpanFull(),
                TextInput::make('categoria')
                    ->label('Categoría')
                    ->datalist([
                        'Derecho Constitucional', 'Derecho Penal', 'Derecho Ambiental',
                        'Derecho Administrativo', 'Derecho Procesal', 'Derecho y Tecnología',
                        'Derecho Civil', 'Derecho Laboral',
                    ]),
                TextInput::make('paginas')
                    ->label('Páginas')
                    ->placeholder('9-42'),
                TextInput::make('doi')
                    ->label('DOI'),
                TextInput::make('descargas')
                    ->label('Descargas')
                    ->numeric()
                    ->default(0),
                Textarea::make('resumen')
                    ->label('Resumen')
                    ->rows(5)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('pdf')
                    ->label('Archivo PDF')
                    ->collection('pdf')
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpanFull(),
                DatePicker::make('fecha')
                    ->label('Fecha de publicación')
                    ->default(now()),
                Toggle::make('publicado')
                    ->label('Publicado')
                    ->default(true)
                    ->helperText('Si está activo, aparece en el sitio público.'),
            ]);
    }
}
