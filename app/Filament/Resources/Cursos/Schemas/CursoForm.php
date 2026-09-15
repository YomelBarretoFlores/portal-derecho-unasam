<?php

namespace App\Filament\Resources\Cursos\Schemas;

use App\Filament\Forms\EditorialStatusSelect;
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
                Select::make('plan')
                    ->label('Plan')
                    ->options(['2023' => 'Plan 2023', '2019' => 'Plan 2019'])
                    ->default('2023')
                    ->required(),
                Select::make('ciclo')
                    ->label('Ciclo')
                    // Doce, no diez: la malla de Derecho son seis años.
                    ->options(array_combine(range(1, Curso::CICLOS), range(1, Curso::CICLOS)))
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
                EditorialStatusSelect::make(),
            ]);
    }
}
