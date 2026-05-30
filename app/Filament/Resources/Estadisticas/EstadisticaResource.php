<?php

namespace App\Filament\Resources\Estadisticas;

use App\Filament\Resources\Estadisticas\Pages\CreateEstadistica;
use App\Filament\Resources\Estadisticas\Pages\EditEstadistica;
use App\Filament\Resources\Estadisticas\Pages\ListEstadisticas;
use App\Filament\Resources\Estadisticas\Schemas\EstadisticaForm;
use App\Filament\Resources\Estadisticas\Tables\EstadisticasTable;
use App\Models\Estadistica;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EstadisticaResource extends Resource
{
    protected static ?string $model = Estadistica::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $recordTitleAttribute = 'anio';

    protected static ?string $modelLabel = 'estadística';

    protected static ?string $pluralModelLabel = 'Estadísticas';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido del sitio';

    public static function form(Schema $schema): Schema
    {
        return EstadisticaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EstadisticasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEstadisticas::route('/'),
            'create' => CreateEstadistica::route('/create'),
            'edit' => EditEstadistica::route('/{record}/edit'),
        ];
    }
}
