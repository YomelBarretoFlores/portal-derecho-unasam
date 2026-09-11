<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Filament\Forms\EditorialStatusSelect;
use App\Filament\Forms\UrlDeRespaldo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->afterStateUpdated(function (?string $state, ?string $old, Get $get, Set $set): void {
                        if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Se genera automáticamente desde el título.'),
                SpatieMediaLibraryFileUpload::make('imagen')
                    ->label('Imagen de portada')
                    ->collection('imagen')
                    ->image()
                    ->imageEditor()
                    ->maxSize(config('media.max_image_kb'))
                    ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                    ->helperText(fn (): string => UrlDeRespaldo::textoDeCarga('JPG, PNG o WebP.'))
                    ->columnSpanFull(),
                UrlDeRespaldo::imagen('imagen_url_respaldo', 'URL pública de respaldo de la imagen'),
                Textarea::make('extracto')
                    ->label('Extracto')
                    ->rows(3)
                    ->maxLength(300)
                    ->columnSpanFull(),
                RichEditor::make('contenido')
                    ->label('Contenido')
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                    ->columnSpanFull(),
                TextInput::make('autor')
                    ->label('Autor'),
                TextInput::make('tiempo_lectura')
                    ->label('Tiempo de lectura')
                    ->placeholder('3 min'),
                DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                    ->default(now()),
                EditorialStatusSelect::make(),
            ]);
    }
}
