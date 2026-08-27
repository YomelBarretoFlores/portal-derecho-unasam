<?php

namespace App\Filament\Resources\RevistaNumeros;

use App\Filament\Actions\PreviewActions;
use App\Filament\Forms\EditorialStatusSelect;
use App\Filament\Resources\RevistaNumeros\Pages\CreateRevistaNumero;
use App\Filament\Resources\RevistaNumeros\Pages\EditRevistaNumero;
use App\Filament\Resources\RevistaNumeros\Pages\ListRevistaNumeros;
use App\Filament\Tables\EditorialStatusColumn;
use App\Models\RevistaNumero;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RevistaNumeroResource extends Resource
{
    protected static ?string $model = RevistaNumero::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bookmark-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?string $modelLabel = 'número';

    protected static ?string $pluralModelLabel = 'Números';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('revista_id')->label('Revista')->relationship('revista', 'nombre')->required()->preload(),
            TextInput::make('volumen')->required()->maxLength(30),
            TextInput::make('numero')->label('Número')->required()->maxLength(30),
            TextInput::make('titulo')->required()->live(onBlur: true)
                ->afterStateUpdated(function (?string $state, ?string $old, Get $get, Set $set): void {
                    if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                        $set('slug', Str::slug((string) $state));
                    }
                })->columnSpanFull(),
            TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
            TextInput::make('subtitulo')->label('Subtítulo')->columnSpanFull(),
            Textarea::make('descripcion')->label('Descripción')->columnSpanFull(),
            DatePicker::make('fecha_publicacion')->label('Fecha de publicación')
                ->required(fn (Get $get): bool => $get('estado_editorial') === 'published'),
            TextInput::make('orden')->numeric()->default(0),
            Toggle::make('es_actual')->label('Número actual')
                ->helperText('Al activarlo, cualquier otro número actual de la revista se convertirá en archivo.'),
            SpatieMediaLibraryFileUpload::make('portada')->collection('portada')->image()
                ->maxSize(config('media.max_image_kb'))->disabled(fn (): bool => ! config('media.uploads_enabled')),
            SpatieMediaLibraryFileUpload::make('numero_pdf')->label('PDF del número')->collection('numero_pdf')
                ->acceptedFileTypes(['application/pdf'])->maxSize(config('media.max_pdf_kb'))
                ->disabled(fn (): bool => ! config('media.uploads_enabled')),
            EditorialStatusSelect::make(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('revista.nombre')->label('Revista'),
            TextColumn::make('volumen')->label('Vol.'),
            TextColumn::make('numero')->label('Núm.'),
            TextColumn::make('titulo')->searchable(),
            TextColumn::make('fecha_publicacion')->date()->sortable(),
            TextColumn::make('es_actual')->label('Ubicación')->badge()
                ->formatStateUsing(fn (bool $state): string => $state ? 'Actual' : 'Archivo')
                ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            EditorialStatusColumn::make(),
        ])->defaultSort('fecha_publicacion', 'desc')->recordActions([
            PreviewActions::preview(),
            PreviewActions::published(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRevistaNumeros::route('/'),
            'create' => CreateRevistaNumero::route('/create'),
            'edit' => EditRevistaNumero::route('/{record}/edit'),
        ];
    }
}
