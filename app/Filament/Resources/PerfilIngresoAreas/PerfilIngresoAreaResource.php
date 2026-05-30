<?php

namespace App\Filament\Resources\PerfilIngresoAreas;

use App\Filament\Resources\PerfilIngresoAreas\Pages\CreatePerfilIngresoArea;
use App\Filament\Resources\PerfilIngresoAreas\Pages\EditPerfilIngresoArea;
use App\Filament\Resources\PerfilIngresoAreas\Pages\ListPerfilIngresoAreas;
use App\Filament\Resources\PerfilIngresoAreas\Schemas\PerfilIngresoAreaForm;
use App\Filament\Resources\PerfilIngresoAreas\Tables\PerfilIngresoAreasTable;
use App\Models\PerfilIngresoArea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PerfilIngresoAreaResource extends Resource
{
    protected static ?string $model = PerfilIngresoArea::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static ?string $modelLabel = 'área de perfil de ingreso';

    protected static ?string $pluralModelLabel = 'Perfil de ingreso';

    protected static string|\UnitEnum|null $navigationGroup = 'Páginas institucionales';

    public static function form(Schema $schema): Schema
    {
        return PerfilIngresoAreaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PerfilIngresoAreasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPerfilIngresoAreas::route('/'),
            'create' => CreatePerfilIngresoArea::route('/create'),
            'edit' => EditPerfilIngresoArea::route('/{record}/edit'),
        ];
    }
}
