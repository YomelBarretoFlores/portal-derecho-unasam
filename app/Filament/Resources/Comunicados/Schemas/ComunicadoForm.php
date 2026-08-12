<?php

namespace App\Filament\Resources\Comunicados\Schemas;

use App\Filament\Forms\EditorialStatusSelect;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->label('Imagen destacada')
                    ->collection('imagen')
                    ->image()
                    ->imageEditor()
                    ->maxSize(config('media.max_image_kb'))
                    ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                    ->helperText(fn (): string => config('media.uploads_enabled') ? 'JPG, PNG o WebP.' : 'Las cargas están deshabilitadas en este entorno.')
                    ->columnSpanFull(),
                Textarea::make('resumen')
                    ->label('Resumen')
                    ->rows(2)
                    ->maxLength(300)
                    ->columnSpanFull(),
                RichEditor::make('contenido')
                    ->label('Contenido')
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                    ->columnSpanFull(),
                EditorialStatusSelect::make(),
                DateTimePicker::make('fecha_publicacion')
                    ->label('Fecha de publicación')
                    ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                    ->default(now()),
            ]);
    }
}
