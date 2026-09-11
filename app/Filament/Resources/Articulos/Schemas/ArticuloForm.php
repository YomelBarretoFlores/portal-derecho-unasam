<?php

namespace App\Filament\Resources\Articulos\Schemas;

use App\Filament\Forms\EditorialStatusSelect;
use App\Rules\SafeUrl;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->afterStateUpdated(function (?string $state, ?string $old, Get $get, Set $set): void {
                        if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                            $set('slug', Str::slug((string) $state));
                        }
                    })
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->helperText('Se genera automáticamente desde el título.'),
                TagsInput::make('autores')
                    ->label('Autores')
                    ->placeholder('Añadir autor')
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                    ->columnSpanFull(),
                Select::make('revista_numero_id')
                    ->label('Número de revista')
                    ->relationship('numero', 'titulo')
                    ->searchable()
                    ->preload()
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published'),
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
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                    ->columnSpanFull(),
                RichEditor::make('contenido')
                    ->label('Contenido del artículo')
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published' && blank($get('pdf')))
                    ->helperText('Puedes publicar contenido web, PDF o ambos.')
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('pdf')
                    ->label('Archivo PDF')
                    ->collection('pdf')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published' && blank($get('contenido')) && blank($get('pdf_url_respaldo')))
                    ->maxSize(config('media.max_pdf_kb'))
                    ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                    ->helperText(fn (): string => config('media.uploads_enabled') ? 'PDF opcional si existe contenido web.' : 'Las cargas están deshabilitadas en este entorno; usa la URL de respaldo.')
                    ->columnSpanFull(),
                TextInput::make('pdf_url_respaldo')
                    ->label('URL pública de respaldo del PDF')
                    ->rule(new SafeUrl)
                    ->helperText('Alternativa al archivo subido. Basta con una de las dos para publicar sin contenido web.')
                    ->columnSpanFull(),
                DatePicker::make('fecha')
                    ->label('Fecha de publicación')
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                    ->default(now()),
                EditorialStatusSelect::make(),
            ]);
    }
}
