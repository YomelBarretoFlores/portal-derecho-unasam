<?php

namespace App\Filament\Resources\RevistaAvisos;

use App\Filament\Forms\EditorialStatusSelect;
use App\Filament\Forms\UrlDeRespaldo;
use App\Filament\Resources\RevistaAvisos\Pages\CreateRevistaAviso;
use App\Filament\Resources\RevistaAvisos\Pages\EditRevistaAviso;
use App\Filament\Resources\RevistaAvisos\Pages\ListRevistaAvisos;
use App\Filament\Tables\EditorialStatusColumn;
use App\Models\Revista;
use App\Models\RevistaAviso;
use App\Rules\SafeUrl;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevistaAvisoResource extends Resource
{
    protected static ?string $model = RevistaAviso::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'aviso';

    protected static ?string $pluralModelLabel = 'Avisos de la revista';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('revista_id')->relationship('revista', 'nombre')->default(fn () => Revista::query()->value('id'))->required()->preload(),
            TextInput::make('titulo')->required()->columnSpanFull(), TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
            Textarea::make('resumen')->columnSpanFull(), RichEditor::make('contenido')->columnSpanFull(),
            DateTimePicker::make('fecha_publicacion'), DateTimePicker::make('fecha_caducidad'),
            TextInput::make('enlace')->rule(new SafeUrl)->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('adjunto')->collection('adjunto')->maxSize(config('media.max_pdf_kb'))->disabled(fn (): bool => ! config('media.uploads_enabled')),
            UrlDeRespaldo::archivo('adjunto_url_respaldo', 'Dirección web del adjunto'),
            EditorialStatusSelect::make(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('titulo')->searchable(), TextColumn::make('fecha_publicacion')->dateTime('d/m/Y H:i')->sortable(), TextColumn::make('fecha_caducidad')->date('d/m/Y'), EditorialStatusColumn::make(),
        ])->defaultSort('fecha_publicacion', 'desc')->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRevistaAvisos::route('/'), 'create' => CreateRevistaAviso::route('/create'), 'edit' => EditRevistaAviso::route('/{record}/edit')];
    }
}
