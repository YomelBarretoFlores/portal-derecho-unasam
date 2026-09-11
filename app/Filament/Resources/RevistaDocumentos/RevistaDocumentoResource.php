<?php

namespace App\Filament\Resources\RevistaDocumentos;

use App\Filament\Forms\EditorialStatusSelect;
use App\Filament\Forms\UrlDeRespaldo;
use App\Filament\Resources\RevistaDocumentos\Pages\CreateRevistaDocumento;
use App\Filament\Resources\RevistaDocumentos\Pages\EditRevistaDocumento;
use App\Filament\Resources\RevistaDocumentos\Pages\ListRevistaDocumentos;
use App\Filament\Tables\EditorialStatusColumn;
use App\Models\Revista;
use App\Models\RevistaDocumento;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevistaDocumentoResource extends Resource
{
    protected static ?string $model = RevistaDocumento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'documento de revista';

    protected static ?string $pluralModelLabel = 'Documentos y formatos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('revista_id')->relationship('revista', 'nombre')->default(fn () => Revista::query()->value('id'))->required()->preload(),
            Select::make('categoria')->options(RevistaDocumento::CATEGORIAS)->required(),
            TextInput::make('titulo')->required()->maxLength(255)->columnSpanFull(),
            Textarea::make('descripcion')->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('archivo')->collection('archivo')
                ->acceptedFileTypes(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->maxSize(config('media.max_pdf_kb'))->disabled(fn (): bool => ! config('media.uploads_enabled'))
                ->helperText(fn (): string => UrlDeRespaldo::textoDeCarga('PDF o documento de Word.')),
            UrlDeRespaldo::archivo('url', 'Dirección web del documento'),
            TextInput::make('orden')->numeric()->default(0),
            Toggle::make('visible')->default(true),
            EditorialStatusSelect::make(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('titulo')->searchable(),
            TextColumn::make('categoria')->formatStateUsing(fn (string $state) => RevistaDocumento::CATEGORIAS[$state] ?? $state)->badge(),
            TextColumn::make('orden')->sortable(),
            IconColumn::make('visible')->boolean(),
            EditorialStatusColumn::make(),
        ])->defaultSort('orden')->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRevistaDocumentos::route('/'), 'create' => CreateRevistaDocumento::route('/create'), 'edit' => EditRevistaDocumento::route('/{record}/edit')];
    }
}
