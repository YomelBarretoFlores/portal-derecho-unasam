<?php

namespace App\Filament\Resources\Objetivos;

use App\Filament\Resources\Objetivos\Pages\CreateObjetivo;
use App\Filament\Resources\Objetivos\Pages\EditObjetivo;
use App\Filament\Resources\Objetivos\Pages\ListObjetivos;
use App\Filament\Resources\Objetivos\Schemas\ObjetivoForm;
use App\Filament\Resources\Objetivos\Tables\ObjetivosTable;
use App\Models\Objetivo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ObjetivoResource extends Resource
{
    protected static ?string $model = Objetivo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $recordTitleAttribute = 'texto';

    protected static ?string $modelLabel = 'objetivo educacional';

    protected static ?string $pluralModelLabel = 'Objetivos educacionales';

    protected static string|\UnitEnum|null $navigationGroup = 'Páginas institucionales';

    public static function form(Schema $schema): Schema
    {
        return ObjetivoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ObjetivosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListObjetivos::route('/'),
            'create' => CreateObjetivo::route('/create'),
            'edit' => EditObjetivo::route('/{record}/edit'),
        ];
    }
}
