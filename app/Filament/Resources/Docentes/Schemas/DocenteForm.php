<?php

namespace App\Filament\Resources\Docentes\Schemas;

use App\Filament\Forms\EditorialStatusSelect;
use App\Rules\SafeUrl;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DocenteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identidad académica')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, ?string $old, Get $get, Set $set): void {
                                if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Se genera desde el nombre y no cambia si luego lo editas manualmente.')
                            ->columnSpanFull(),
                        TextInput::make('grado')
                            ->label('Grado académico')
                            ->placeholder('Doctor en Derecho'),
                        TextInput::make('categoria')
                            ->label('Categoría docente')
                            ->placeholder('Docente auxiliar'),
                        TextInput::make('dedicacion')
                            ->label('Dedicación')
                            ->placeholder('Tiempo completo'),
                        TextInput::make('area')
                            ->label('Área / especialidad'),
                        Textarea::make('resena')
                            ->label('Reseña académica')
                            ->rows(7)
                            ->required(fn (Get $get): bool => $get('estado_editorial') === 'published')
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('foto')
                            ->label('Fotografía institucional')
                            ->collection('foto')
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->maxSize(config('media.max_image_kb'))
                            ->disabled(fn (): bool => ! config('media.uploads_enabled'))
                            ->helperText(fn (): string => config('media.uploads_enabled')
                                ? 'Usa un retrato institucional sin firmas, documentos, capturas ni fondos distractores. JPG, PNG o WebP.'
                                : 'Las cargas están deshabilitadas en este entorno; la fotografía existente continúa visible.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Contacto y perfiles académicos')
                    ->description('Solo se publica el correo institucional. No registres teléfonos ni correos personales.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email_institucional')
                            ->label('Correo institucional')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('orcid')
                            ->label('ORCID')
                            ->url()
                            ->rule(new SafeUrl)
                            ->placeholder('https://orcid.org/0000-0000-0000-0000'),
                        TextInput::make('google_scholar_url')
                            ->label('Google Scholar')
                            ->url()
                            ->rule(new SafeUrl),
                        TextInput::make('cti_vitae_url')
                            ->label('CTI Vitae')
                            ->url()
                            ->rule(new SafeUrl),
                        TextInput::make('perfil_academico_url')
                            ->label('Otro perfil académico')
                            ->url()
                            ->rule(new SafeUrl)
                            ->helperText('Por ejemplo, ALICIA/CONCYTEC. No lo rotules como Google Scholar si no corresponde.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Publicaciones destacadas')
                    ->schema([
                        Repeater::make('publicaciones')
                            ->label('Publicaciones')
                            ->schema([
                                TextInput::make('titulo')->label('Título')->required()->columnSpanFull(),
                                TextInput::make('anio')->label('Año')->numeric()->minValue(1900)->maxValue((int) date('Y') + 1),
                                TextInput::make('url')->label('Enlace')->url()->rule(new SafeUrl),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Añadir publicación')
                            ->columnSpanFull(),
                    ]),

                Section::make('Revisión y publicación')
                    ->description('Los perfiles documentales ingresan como pendientes e inactivos. Verifica la ficha antes de hacerla pública.')
                    ->columns(3)
                    ->schema([
                        EditorialStatusSelect::make(),
                        TextInput::make('orden')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Menor número aparece primero.'),
                        TextInput::make('documento_fuente')
                            ->label('Documento fuente')
                            ->required(fn (Get $get): bool => in_array($get('estado_editorial'), ['verified', 'published'], true))
                            ->helperText('Nombre del DOCX, PDF, resolución u otro documento institucional que respalda la ficha.')
                            ->columnSpanFull(),
                        Textarea::make('observaciones_revision')
                            ->label('Observaciones internas de revisión')
                            ->rows(4)
                            ->helperText('Estas observaciones nunca se muestran en el sitio público.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
