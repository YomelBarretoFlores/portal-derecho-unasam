<?php

namespace App\Filament\Resources\Accesos;

use App\Filament\Resources\Accesos\Pages\CreateAcceso;
use App\Filament\Resources\Accesos\Pages\EditAcceso;
use App\Filament\Resources\Accesos\Pages\ListAccesos;
use App\Filament\Resources\Accesos\Schemas\AccesoForm;
use App\Filament\Resources\Accesos\Tables\AccesosTable;
use App\Models\Acceso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AccesoResource extends Resource
{
    protected static ?string $model = Acceso::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static ?string $modelLabel = 'acceso directo';

    protected static ?string $pluralModelLabel = 'Accesos directos';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido del sitio';

    public static function form(Schema $schema): Schema
    {
        return AccesoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccesosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccesos::route('/'),
            'create' => CreateAcceso::route('/create'),
            'edit' => EditAcceso::route('/{record}/edit'),
        ];
    }
}
