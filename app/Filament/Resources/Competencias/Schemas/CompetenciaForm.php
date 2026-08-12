<?php

namespace App\Filament\Resources\Competencias\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompetenciaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grupo')
                    ->label('Grupo')
                    ->options([
                        'Generales' => 'Generales (CG)',
                        'Específicas' => 'Específicas (CE)',
                    ])
                    ->required(),
                TextInput::make('plan')
                    ->label('Plan')
                    ->required()
                    ->datalist([
                        'Plan de Estudios 2023',
                        'Plan de Estudios 2019',
                    ]),
                Toggle::make('vigente')
                    ->label('Plan vigente'),
                TextInput::make('nombre')
                    ->label('Nombre (opcional)')
                    ->helperText('Solo para competencias específicas con nombre (ej. «Asesoría y consultoría»).')
                    ->columnSpanFull(),
                Textarea::make('texto')
                    ->label('Descripción')
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
