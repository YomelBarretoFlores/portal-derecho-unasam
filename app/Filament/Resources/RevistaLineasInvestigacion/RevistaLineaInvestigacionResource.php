<?php

namespace App\Filament\Resources\RevistaLineasInvestigacion;

use App\Filament\Resources\RevistaLineasInvestigacion\Pages\CreateRevistaLineaInvestigacion;
use App\Filament\Resources\RevistaLineasInvestigacion\Pages\EditRevistaLineaInvestigacion;
use App\Filament\Resources\RevistaLineasInvestigacion\Pages\ListRevistaLineasInvestigacion;
use App\Models\RevistaLineaInvestigacion;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevistaLineaInvestigacionResource extends Resource
{
    protected static ?string $slug = 'revista-lineas-investigacion';

    protected static ?string $model = RevistaLineaInvestigacion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?string $modelLabel = 'línea de investigación';

    protected static ?string $pluralModelLabel = 'Líneas de investigación';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('revista_id')->relationship('revista', 'nombre')->required()->preload(), TextInput::make('nombre')->required(), Textarea::make('descripcion')->columnSpanFull(), TextInput::make('orden')->numeric()->default(0), Toggle::make('activa')->default(true)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('nombre')->searchable(), TextColumn::make('descripcion')->limit(60), TextColumn::make('orden')->sortable(), IconColumn::make('activa')->boolean()])->defaultSort('orden')->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRevistaLineasInvestigacion::route('/'), 'create' => CreateRevistaLineaInvestigacion::route('/create'), 'edit' => EditRevistaLineaInvestigacion::route('/{record}/edit')];
    }
}
