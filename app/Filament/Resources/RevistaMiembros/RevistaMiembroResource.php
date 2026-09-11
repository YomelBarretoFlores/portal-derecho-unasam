<?php

namespace App\Filament\Resources\RevistaMiembros;

use App\Filament\Resources\RevistaMiembros\Pages\CreateRevistaMiembro;
use App\Filament\Resources\RevistaMiembros\Pages\EditRevistaMiembro;
use App\Filament\Resources\RevistaMiembros\Pages\ListRevistaMiembros;
use App\Models\Revista;
use App\Models\RevistaMiembro;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RevistaMiembroResource extends Resource
{
    protected static ?string $model = RevistaMiembro::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'integrante';

    protected static ?string $pluralModelLabel = 'Equipo editorial';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Integrante del equipo editorial')
                ->columns(2)
                ->schema([
                    Select::make('revista_id')->label('Revista')
                        ->relationship('revista', 'nombre')->default(fn () => Revista::query()->value('id'))->required()->preload(),
                    Select::make('grupo')->label('Función editorial')
                        ->options(RevistaMiembro::GRUPOS)->required(),
                    TextInput::make('grado')->placeholder('Dra., Dr., PhD., Mag., Est.'),
                    TextInput::make('nombre')->required()->maxLength(255),
                    TextInput::make('afiliacion')->label('Afiliación institucional')->columnSpanFull(),
                    TextInput::make('pais')->label('País'),
                    TextInput::make('orden')->numeric()->default(0)->minValue(0),
                    TextInput::make('orcid')->label('ORCID')->placeholder('0000-0000-0000-0000'),
                    TextInput::make('email')->label('Correo institucional')->email(),
                    Toggle::make('activo')->label('Visible públicamente')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grupo')->label('Función')
                    ->formatStateUsing(fn (string $state): string => RevistaMiembro::GRUPOS[$state] ?? $state)
                    ->badge(),
                TextColumn::make('grado')->label('Grado'),
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('afiliacion')->label('Afiliación')->searchable()->wrap(),
                TextColumn::make('pais')->label('País'),
                TextColumn::make('orden')->sortable(),
                IconColumn::make('activo')->boolean(),
            ])
            ->filters([
                SelectFilter::make('grupo')->label('Función')->options(RevistaMiembro::GRUPOS),
            ])
            ->defaultSort('orden')
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRevistaMiembros::route('/'),
            'create' => CreateRevistaMiembro::route('/create'),
            'edit' => EditRevistaMiembro::route('/{record}/edit'),
        ];
    }
}
