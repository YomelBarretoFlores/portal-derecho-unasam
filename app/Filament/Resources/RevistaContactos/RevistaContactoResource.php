<?php

namespace App\Filament\Resources\RevistaContactos;

use App\Filament\Resources\RevistaContactos\Pages\CreateRevistaContacto;
use App\Filament\Resources\RevistaContactos\Pages\EditRevistaContacto;
use App\Filament\Resources\RevistaContactos\Pages\ListRevistaContactos;
use App\Models\Revista;
use App\Models\RevistaContacto;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevistaContactoResource extends Resource
{
    protected static ?string $model = RevistaContacto::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?int $navigationSort = 8;

    protected static ?string $modelLabel = 'contacto';

    protected static ?string $pluralModelLabel = 'Contactos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('revista_id')->relationship('revista', 'nombre')->default(fn () => Revista::query()->value('id'))->required()->preload(), Select::make('tipo')->options(RevistaContacto::TIPOS)->required(), TextInput::make('nombre')->required(), TextInput::make('cargo'), TextInput::make('email')->email(), TextInput::make('telefono'), TextInput::make('orden')->numeric()->default(0), Toggle::make('visible')->default(true)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('nombre')->searchable(), TextColumn::make('cargo'), TextColumn::make('email'), TextColumn::make('telefono'), TextColumn::make('orden')->sortable(), IconColumn::make('visible')->boolean()])->defaultSort('orden')->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRevistaContactos::route('/'), 'create' => CreateRevistaContacto::route('/create'), 'edit' => EditRevistaContacto::route('/{record}/edit')];
    }
}
