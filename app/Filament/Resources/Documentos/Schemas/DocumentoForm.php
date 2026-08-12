<?php

namespace App\Filament\Resources\Documentos\Schemas;

use App\Rules\SafeUrl;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('categoria')
                    ->label('Categoría')
                    ->required()
                    ->datalist([
                        'Planes de Estudio',
                        'Reglamentos',
                        'Resoluciones',
                    ]),
                DatePicker::make('fecha')
                    ->label('Fecha'),
                SpatieMediaLibraryFileUpload::make('archivo')
                    ->label('Archivo PDF')
                    ->collection('archivo')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(config('media.max_pdf_kb'))
                    ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                    ->helperText(fn (): string => config('media.uploads_enabled') ? 'PDF, máximo configurado por el portal.' : 'Las cargas están deshabilitadas en este entorno.')
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->label('Enlace externo (si no subes PDF)')
                    ->rule(new SafeUrl)
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
