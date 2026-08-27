<?php

namespace App\Filament\Resources\RevistaEnvios;

use App\Filament\Resources\RevistaEnvios\Pages\EditRevistaEnvio;
use App\Filament\Resources\RevistaEnvios\Pages\ListRevistaEnvios;
use App\Models\RevistaEnvio;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RevistaEnvioResource extends Resource
{
    protected static ?string $model = RevistaEnvio::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?string $modelLabel = 'envío';

    protected static ?string $pluralModelLabel = 'Envíos de manuscritos';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Seguimiento editorial')->columns(2)->schema([
                TextInput::make('codigo_seguimiento')->disabled()->dehydrated(false),
                Select::make('estado')->options(RevistaEnvio::ESTADOS)->required(),
                Textarea::make('observaciones_internas')->label('Observaciones internas')->columnSpanFull(),
            ]),
            Section::make('Autor y contacto')->columns(2)->schema([
                TextInput::make('nombres')->disabled()->dehydrated(false),
                TextInput::make('email_institucional')->disabled()->dehydrated(false),
                TextInput::make('documento_identidad')->label('DNI o pasaporte')->disabled()->dehydrated(false),
                TextInput::make('whatsapp')->disabled()->dehydrated(false),
                TextInput::make('afiliacion')->disabled()->dehydrated(false),
                TextInput::make('orcid')->disabled()->dehydrated(false),
                TextInput::make('ciudad')->disabled()->dehydrated(false),
                TextInput::make('pais')->disabled()->dehydrated(false),
            ]),
            Section::make('Manuscrito')->columns(2)->schema([
                TextInput::make('titulo')->disabled()->dehydrated(false)->columnSpanFull(),
                TextInput::make('tipo_contribucion')->disabled()->dehydrated(false),
                TextInput::make('lineaInvestigacion.nombre')->label('Línea de investigación')->disabled()->dehydrated(false),
                Textarea::make('resumen')->disabled()->dehydrated(false)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('codigo_seguimiento')->label('Código')->searchable()->copyable(),
            TextColumn::make('nombres')->searchable(),
            TextColumn::make('email_institucional')->label('Correo')->searchable(),
            TextColumn::make('documento_enmascarado')->label('Documento'),
            TextColumn::make('whatsapp_enmascarado')->label('WhatsApp'),
            TextColumn::make('titulo')->limit(45)->wrap(),
            TextColumn::make('estado')->badge()->formatStateUsing(fn (string $state): string => RevistaEnvio::ESTADOS[$state] ?? $state),
            TextColumn::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([SelectFilter::make('estado')->options(RevistaEnvio::ESTADOS)])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('correo')->label('Responder')->icon('heroicon-o-envelope')
                    ->url(fn (RevistaEnvio $record): string => 'mailto:'.$record->email_institucional.'?subject='.rawurlencode('Revista Derecho y Cultura · '.$record->codigo_seguimiento).'&body='.rawurlencode("Estimado/a {$record->nombres}:\n\nRespecto de su envío {$record->codigo_seguimiento}:\n\n")),
                EditAction::make()->label('Gestionar'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRevistaEnvios::route('/'), 'edit' => EditRevistaEnvio::route('/{record}/edit')];
    }
}
