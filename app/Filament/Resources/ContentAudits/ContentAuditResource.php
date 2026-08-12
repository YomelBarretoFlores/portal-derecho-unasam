<?php

namespace App\Filament\Resources\ContentAudits;

use App\Filament\Resources\ContentAudits\Pages\ListContentAudits;
use App\Models\ContentAudit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentAuditResource extends Resource
{
    protected static ?string $model = ContentAudit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';

    protected static ?string $modelLabel = 'registro de auditoría';

    protected static ?string $pluralModelLabel = 'Auditoría';

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
            TextColumn::make('user.email')->label('Usuario')->placeholder('Sistema'),
            TextColumn::make('event')->label('Evento')->badge(),
            TextColumn::make('auditable_type')->label('Entidad')->formatStateUsing(fn (string $state): string => class_basename($state)),
            TextColumn::make('auditable_id')->label('ID'),
            TextColumn::make('ip_address')->label('IP'),
            TextColumn::make('new_values')->label('Cambios')->formatStateUsing(fn ($state): string => json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->limit(80)->wrap(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListContentAudits::route('/')];
    }
}
