<?php

namespace App\Filament\Resources\AreasLaborales;

use App\Filament\Resources\AreasLaborales\Pages\CreateAreaLaboral;
use App\Filament\Resources\AreasLaborales\Pages\EditAreaLaboral;
use App\Filament\Resources\AreasLaborales\Pages\ListAreasLaborales;
use App\Filament\Resources\AreasLaborales\Schemas\AreaLaboralForm;
use App\Filament\Resources\AreasLaborales\Tables\AreasLaboralesTable;
use App\Models\AreaLaboral;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AreaLaboralResource extends Resource
{
    protected static ?string $model = AreaLaboral::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static ?string $modelLabel = 'área laboral';

    protected static ?string $pluralModelLabel = 'Campo laboral';

    protected static string|\UnitEnum|null $navigationGroup = 'Páginas institucionales';

    public static function form(Schema $schema): Schema
    {
        return AreaLaboralForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AreasLaboralesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAreasLaborales::route('/'),
            'create' => CreateAreaLaboral::route('/create'),
            'edit' => EditAreaLaboral::route('/{record}/edit'),
        ];
    }
}
