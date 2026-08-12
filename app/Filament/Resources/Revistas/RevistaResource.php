<?php

namespace App\Filament\Resources\Revistas;

use App\Filament\Actions\PreviewActions;
use App\Filament\Forms\EditorialStatusSelect;
use App\Filament\Resources\Revistas\Pages\CreateRevista;
use App\Filament\Resources\Revistas\Pages\EditRevista;
use App\Filament\Resources\Revistas\Pages\ListRevistas;
use App\Filament\Tables\EditorialStatusColumn;
use App\Models\Revista;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevistaResource extends Resource
{
    protected static ?string $model = Revista::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Revista Derecho y Cultura';

    protected static ?string $modelLabel = 'revista';

    protected static ?string $pluralModelLabel = 'Revista';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identidad y presentación')
                ->columns(2)
                ->schema([
                    TextInput::make('nombre')->label('Nombre oficial')->required()->maxLength(255),
                    TextInput::make('nombre_corto')->label('Nombre corto')->required()->maxLength(255),
                    RichEditor::make('presentacion')->label('Presentación')->columnSpanFull(),
                    RichEditor::make('enfoque_alcance')->label('Enfoque y alcance')->columnSpanFull(),
                    TextInput::make('unidad_responsable')->label('Unidad responsable')->columnSpanFull(),
                    TextInput::make('contacto_email')->label('Correo de contacto')->email(),
                    TextInput::make('issn')->label('ISSN en línea')
                        ->helperText('Déjalo vacío hasta que el registro ISSN haya sido asignado oficialmente.'),
                ]),

            Section::make('Política editorial')
                ->columns(2)
                ->schema([
                    TextInput::make('periodicidad'),
                    TextInput::make('modalidad')->placeholder('Digital'),
                    Select::make('idiomas')->multiple()->options([
                        'es' => 'Español',
                        'en' => 'Inglés',
                    ])->columnSpanFull(),
                    Select::make('tipos_contribucion')->label('Tipos de contribución')->multiple()->options([
                        'articulo_original' => 'Artículo de investigación original',
                        'articulo_revision' => 'Artículo de revisión',
                        'ensayo_academico' => 'Ensayo académico',
                        'resena' => 'Reseña',
                    ])->columnSpanFull(),
                    TextInput::make('sistema_arbitraje')->label('Sistema de arbitraje'),
                    TextInput::make('norma_citacion')->label('Norma de citación'),
                    RichEditor::make('normas_publicacion')->label('Normas para autores')->columnSpanFull(),
                ]),

            Section::make('Resolución de creación')
                ->columns(2)
                ->schema([
                    TextInput::make('resolucion_numero')->label('Número de resolución'),
                    DatePicker::make('resolucion_fecha')->label('Fecha de resolución'),
                    Textarea::make('resolucion_resumen')->label('Resumen de la resolución')->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('resolucion')
                        ->label('PDF de resolución')->collection('resolucion')
                        ->acceptedFileTypes(['application/pdf'])->maxSize(config('media.max_pdf_kb'))
                        ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                        ->helperText(fn (): string => config('media.uploads_enabled')
                            ? 'Documento institucional aprobado. Solo se admite PDF.'
                            : 'Las cargas están deshabilitadas en este entorno; el documento existente sigue disponible.'),
                ]),

            Section::make('Publicación')
                ->columns(3)
                ->schema([
                    SpatieMediaLibraryFileUpload::make('logo')
                        ->collection('logo')->image()->maxSize(config('media.max_image_kb'))
                        ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                        ->helperText('No utilices los escudos institucionales como logo propio de la revista.'),
                    EditorialStatusSelect::make()->columnSpan(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nombre')->searchable(),
            TextColumn::make('resolucion_numero')->label('Resolución'),
            EditorialStatusColumn::make(),
        ])->recordActions([
            PreviewActions::preview(),
            PreviewActions::published(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRevistas::route('/'),
            'create' => CreateRevista::route('/create'),
            'edit' => EditRevista::route('/{record}/edit'),
        ];
    }
}
