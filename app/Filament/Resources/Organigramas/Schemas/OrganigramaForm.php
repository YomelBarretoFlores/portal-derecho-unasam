<?php

namespace App\Filament\Resources\Organigramas\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrganigramaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título')
                    ->columnSpanFull(),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('imagen')
                    ->label('Imagen del organigrama')
                    ->collection('imagen')
                    ->image()
                    ->imageEditor()
                    ->maxSize(config('media.max_image_kb'))
                    ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                    ->columnSpanFull()
                    ->helperText(fn (): string => config('media.uploads_enabled') ? 'Sube la imagen del organigrama (JPG/PNG).' : 'Las cargas están deshabilitadas en este entorno.'),
            ]);
    }
}
