<?php

namespace App\Filament\Resources\Objetivos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ObjetivoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('plan')
                    ->label('Plan')
                    ->required()
                    ->datalist([
                        'Plan de Estudios 2023',
                        'Plan de Estudios 2019',
                    ]),
                Toggle::make('vigente')
                    ->label('Plan vigente')
                    ->helperText('Marca el plan actual (muestra la etiqueta «Vigente»).'),
                Textarea::make('texto')
                    ->label('Objetivo')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
