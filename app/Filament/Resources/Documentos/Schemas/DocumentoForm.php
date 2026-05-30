<?php

namespace App\Filament\Resources\Documentos\Schemas;

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
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->label('Enlace externo (si no subes PDF)')
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
