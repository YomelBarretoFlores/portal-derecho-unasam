<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'noticia' => 'Noticia',
                        'opinion' => 'Opinión',
                        'evento' => 'Evento',
                    ])
                    ->default('noticia')
                    ->required(),
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
                    ->label('Imagen de portada')
                    ->collection('imagen')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),
                Textarea::make('extracto')
                    ->label('Extracto')
                    ->rows(3)
                    ->maxLength(300)
                    ->columnSpanFull(),
                RichEditor::make('contenido')
                    ->label('Contenido')
                    ->columnSpanFull(),
                TextInput::make('autor')
                    ->label('Autor'),
                TextInput::make('tiempo_lectura')
                    ->label('Tiempo de lectura')
                    ->placeholder('3 min'),
                DatePicker::make('fecha')
                    ->label('Fecha')
                    ->default(now()),
                Toggle::make('publicado')
                    ->label('Publicado')
                    ->default(true)
                    ->helperText('Si está activo, aparece en el sitio público.'),
            ]);
    }
}
