<?php

namespace App\Filament\Resources\Organigramas;

use App\Filament\Resources\Organigramas\Pages\EditOrganigrama;
use App\Filament\Resources\Organigramas\Pages\ListOrganigramas;
use App\Filament\Resources\Organigramas\Schemas\OrganigramaForm;
use App\Filament\Resources\Organigramas\Tables\OrganigramasTable;
use App\Models\Organigrama;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrganigramaResource extends Resource
{
    protected static ?string $model = Organigrama::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-share';

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static ?string $modelLabel = 'organigrama';

    protected static ?string $pluralModelLabel = 'Organigrama';

    protected static string|\UnitEnum|null $navigationGroup = 'Páginas institucionales';

    public static function form(Schema $schema): Schema
    {
        return OrganigramaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganigramasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    // Singleton: sin página de creación; se edita la única fila.
    public static function getPages(): array
    {
        return [
            'index' => ListOrganigramas::route('/'),
            'edit' => EditOrganigrama::route('/{record}/edit'),
        ];
    }
}
