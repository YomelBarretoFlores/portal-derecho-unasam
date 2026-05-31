<?php

namespace App\Filament\Resources\Cursos\Schemas;

use App\Models\Curso;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ciclo')
                    ->label('Ciclo')
                    ->options(array_combine(range(1, 10), range(1, 10)))
                    ->required(),
                TextInput::make('nombre')
                    ->label('Nombre del curso')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('creditos')
                    ->label('Créditos')
                    ->numeric()
                    ->minValue(0),
                Select::make('tipo')
                    ->label('Tipo')
                    ->options(Curso::TIPOS),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->helperText('Orden dentro del ciclo.'),
            ]);
    }
}
