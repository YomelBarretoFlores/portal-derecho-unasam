<?php

namespace App\Filament\Resources\Estadisticas\Schemas;

use App\Models\Estadistica;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EstadisticaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->label('Indicador')
                    ->options(Estadistica::TIPOS)
                    ->required(),
                TextInput::make('anio')
                    ->label('Año')
                    ->numeric()
                    ->minValue(1986)
                    ->maxValue(2100)
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
