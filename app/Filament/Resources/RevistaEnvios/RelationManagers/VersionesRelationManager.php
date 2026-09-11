<?php

namespace App\Filament\Resources\RevistaEnvios\RelationManagers;

use App\Models\RevistaEnvioVersion;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Versiones corregidas que el autor sube desde el portal público. Solo la nota
 * interna es editable: el archivo y su numeración los fija RevistaSubmissionService.
 */
class VersionesRelationManager extends RelationManager
{
    protected static string $relationship = 'versiones';

    protected static ?string $title = 'Versiones corregidas';

    protected static ?string $modelLabel = 'versión corregida';

    protected static ?string $pluralModelLabel = 'versiones corregidas';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('nota_autor')->label('Nota del autor')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero')
            ->columns([
                TextColumn::make('numero')->label('Versión')->sortable(),
                TextColumn::make('recibida_at')->label('Recibida')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('nota_autor')->label('Nota del autor')->limit(60)->wrap()->placeholder('Sin nota'),
            ])
            ->defaultSort('numero', 'desc')
            ->recordActions([
                Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (RevistaEnvioVersion $record): string => route('revista.envios.admin.version', [$record->revista_envio_id, $record]))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ]);
    }
}
