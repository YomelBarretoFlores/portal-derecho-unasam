<?php

namespace App\Filament\Resources\Comunicados;

use App\Filament\Resources\Comunicados\Pages\CreateComunicado;
use App\Filament\Resources\Comunicados\Pages\EditComunicado;
use App\Filament\Resources\Comunicados\Pages\ListComunicados;
use App\Filament\Resources\Comunicados\Schemas\ComunicadoForm;
use App\Filament\Resources\Comunicados\Tables\ComunicadosTable;
use App\Models\Comunicado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ComunicadoResource extends Resource
{
    protected static ?string $model = Comunicado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static ?string $modelLabel = 'comunicado';

    protected static ?string $pluralModelLabel = 'comunicados';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido del sitio';

    public static function form(Schema $schema): Schema
    {
        return ComunicadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComunicadosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComunicados::route('/'),
            'create' => CreateComunicado::route('/create'),
            'edit' => EditComunicado::route('/{record}/edit'),
        ];
    }
}
