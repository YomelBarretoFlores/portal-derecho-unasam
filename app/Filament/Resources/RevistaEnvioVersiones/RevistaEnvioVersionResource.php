<?php

namespace App\Filament\Resources\RevistaEnvioVersiones;

use App\Filament\Resources\RevistaEnvioVersiones\Pages\EditRevistaEnvioVersion;
use App\Filament\Resources\RevistaEnvioVersiones\Pages\ListRevistaEnvioVersiones;
use App\Models\RevistaEnvioVersion;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevistaEnvioVersionResource extends Resource
{
    protected static ?string $model = RevistaEnvioVersion::class;

    protected static ?string $slug = 'revista-envio-versiones';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?string $modelLabel = 'versión corregida';

    protected static ?string $pluralModelLabel = 'Versiones corregidas';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('envio.codigo_seguimiento')->label('Código')->disabled()->dehydrated(false),
            TextInput::make('numero')->label('Versión')->disabled()->dehydrated(false),
            DateTimePicker::make('recibida_at')->label('Recibida')->disabled()->dehydrated(false),
            Textarea::make('nota_autor')->label('Nota del autor')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('envio.codigo_seguimiento')->label('Código')->searchable(), TextColumn::make('envio.nombres')->label('Autor')->searchable(),
            TextColumn::make('numero')->label('Versión'), TextColumn::make('recibida_at')->label('Recibida')->dateTime('d/m/Y H:i')->sortable(),
        ])->defaultSort('recibida_at', 'desc')->recordActions([
            Action::make('descargar')->icon('heroicon-o-arrow-down-tray')->url(fn (RevistaEnvioVersion $record): string => route('revista.envios.admin.version', [$record->envio, $record])), EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRevistaEnvioVersiones::route('/'), 'edit' => EditRevistaEnvioVersion::route('/{record}/edit')];
    }
}
