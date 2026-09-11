<?php

namespace App\Filament\Pages;

use App\Filament\Forms\UrlDeRespaldo;
use App\Models\Setting;
use App\Rules\SafeUrl;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Página de «Ajustes del sitio»: edita todos los textos sueltos (prosa) del sitio,
 * guardados como pares clave-valor en la tabla settings.
 *
 * Usa el sistema de formularios nativo de Filament (Schema + componentes), de modo
 * que hereda los estilos del panel y es responsive sin CSS propio.
 *
 * @property-read Schema $form
 */
class AjustesSitio extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Páginas institucionales';

    protected static ?string $title = 'Ajustes del sitio';

    protected static ?string $navigationLabel = 'Ajustes del sitio';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.ajustes-sitio';

    /**
     * Estado del formulario (clave => valor).
     *
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessPanel(filament()->getCurrentPanel()) ?? false;
    }

    /**
     * Claves persistidas en la tabla settings.
     *
     * @return array<int, string>
     */
    protected function claves(): array
    {
        return [
            // Páginas institucionales
            'presentacion_titulo', 'presentacion_cuerpo', 'datos_programa',
            'mision', 'vision',
            'historia_trayectoria_titulo', 'historia_trayectoria_cuerpo',
            'resumen_titulo', 'resumen_cuerpo', 'resumen_cita1', 'resumen_cita2', 'resumen_cierre',
            'perfil_ingreso_especifico', 'perfil_egreso_2023', 'perfil_egreso_2019',
            // Inicio
            'home_hero_titulo', 'home_hero_subtitulo', 'home_hero_cta1', 'home_hero_cta2',
            'home_hero_stat1_label', 'home_hero_stat1_valor', 'home_hero_stat1_sufijo',
            'home_hero_stat2_label', 'home_hero_stat2_valor', 'home_hero_stat2_sufijo',
            'home_hero_foto_url', 'home_hero_mascota_url', 'home_hero_mascota_alt',
            'error404_mascota_url',
            'home_about_eyebrow', 'home_about_titulo', 'home_about_cuerpo', 'home_about_cita',
            'home_accesos_eyebrow', 'home_accesos_titulo',
            'home_stats_eyebrow', 'home_stats_titulo', 'home_stats_narrativa',
            'home_revista_eyebrow', 'home_revista_titulo',
            'home_blog_eyebrow', 'home_blog_titulo',
            'home_marquee',
            // Footer / SEO
            'footer_marca', 'footer_descripcion', 'contacto_direccion', 'contacto_telefono',
            'contacto_email', 'footer_cta_texto', 'footer_cta_url', 'lema',
            'seo_title', 'seo_description',
            // Plan de Estudios
            'plan_grado', 'plan_titulo_prof', 'plan_modalidad',
            'plan_pdf_url', 'plan_sga_url', 'plan_intro',
        ];
    }

    public function mount(): void
    {
        $valores = [];

        foreach ($this->claves() as $clave) {
            $valores[$clave] = (string) Setting::get($clave, '');
        }

        $this->form->fill($valores);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Presentación')
                    ->schema([
                        TextInput::make('presentacion_titulo')->label('Título'),
                        Textarea::make('presentacion_cuerpo')->label('Cuerpo (puedes usar varios párrafos)')->rows(6),
                        Textarea::make('datos_programa')->label('Datos del programa')->rows(6)
                            ->placeholder('Una línea por dato. Ej.: Duración | 5 años (10 ciclos)')
                            ->helperText('Una línea por dato, con el formato «Etiqueta | Valor».'),
                    ]),

                Section::make('Misión y Visión')
                    ->columns(2)
                    ->schema([
                        Textarea::make('mision')->label('Misión')->rows(6),
                        Textarea::make('vision')->label('Visión')->rows(6),
                    ]),

                Section::make('Historia — reseña «Nuestra trayectoria»')
                    ->description('Los hitos de la línea de tiempo se editan en «Historia (hitos)».')
                    ->schema([
                        TextInput::make('historia_trayectoria_titulo')->label('Título'),
                        Textarea::make('historia_trayectoria_cuerpo')->label('Cuerpo')->rows(8),
                    ]),

                Section::make('Resumen del Programa')
                    ->schema([
                        TextInput::make('resumen_titulo')->label('Título'),
                        Textarea::make('resumen_cuerpo')->label('Cuerpo principal')->rows(5),
                        Textarea::make('resumen_cita1')->label('Cita legal 1 (Art. 40 — Ley 30220)')->rows(4),
                        Textarea::make('resumen_cita2')->label('Cita legal 2 (Art. 79 — 2015)')->rows(4),
                        Textarea::make('resumen_cierre')->label('Párrafo de cierre')->rows(3),
                    ]),

                Section::make('Perfiles')
                    ->description('Las áreas del perfil de ingreso se editan en «Perfil de ingreso».')
                    ->schema([
                        Textarea::make('perfil_ingreso_especifico')->label('Perfil de ingreso específico')->rows(3),
                        Textarea::make('perfil_egreso_2023')->label('Perfil de egreso 2023 (vigente)')->rows(6),
                        Textarea::make('perfil_egreso_2019')->label('Perfil de egreso 2019')->rows(6),
                    ]),

                Section::make('Inicio — Hero')
                    ->description('El bloque principal de la portada (lo primero que se ve).')
                    ->columns(2)
                    ->schema([
                        TextInput::make('home_hero_titulo')->label('Título')->columnSpanFull(),
                        Textarea::make('home_hero_subtitulo')->label('Subtítulo')->rows(2)->columnSpanFull(),
                        TextInput::make('home_hero_cta1')->label('Botón 1'),
                        TextInput::make('home_hero_cta2')->label('Botón 2'),
                        TextInput::make('home_hero_stat1_label')->label('Cifra 1 — etiqueta'),
                        TextInput::make('home_hero_stat1_valor')->label('Cifra 1 — valor')
                            ->helperText('Debe ser un número; se anima al cargar.'),
                        TextInput::make('home_hero_stat1_sufijo')->label('Cifra 1 — sufijo')
                            ->helperText('Opcional, p. ej. «+».'),
                        TextInput::make('home_hero_stat2_label')->label('Cifra 2 — etiqueta'),
                        TextInput::make('home_hero_stat2_valor')->label('Cifra 2 — valor'),
                        TextInput::make('home_hero_stat2_sufijo')->label('Cifra 2 — sufijo'),
                        UrlDeRespaldo::imagen('home_hero_foto_url', 'Fotografía de fondo')
                            ->helperText('Déjelo vacío para usar la fotografía del patio que viene con el portal, '
                                .'que está optimizada en varios tamaños. Una imagen propia debe ser apaisada y '
                                .'de al menos 2000 px de ancho.'),
                        UrlDeRespaldo::imagen('home_hero_mascota_url', 'Mascota — imagen')
                            ->helperText('Aparece de pie a la derecha del hero, solo en pantallas grandes. '
                                .'Use un PNG o WebP con fondo transparente. Déjelo vacío para no mostrarla.'),
                        TextInput::make('home_hero_mascota_alt')
                            ->label('Mascota — descripción')
                            ->maxLength(120)
                            ->helperText('Para quien navega con lector de pantalla. Si lo deja vacío, '
                                .'la imagen se trata como decorativa y el lector la omite.')
                            ->columnSpanFull(),
                        UrlDeRespaldo::imagen('error404_mascota_url', 'Mascota de la página de error')
                            ->helperText('Acompaña la página que ve quien llega por un enlace roto. '
                                .'Va aparte de la del hero porque ahí la mascota cae desde arriba, y la '
                                .'versión volando encaja mejor que la de pie. Déjelo vacío para no mostrarla.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Inicio — Sección «El programa»')
                    ->columns(2)
                    ->schema([
                        TextInput::make('home_about_eyebrow')->label('Antetítulo'),
                        TextInput::make('home_about_titulo')->label('Título'),
                        Textarea::make('home_about_cuerpo')->label('Cuerpo (varios párrafos)')->rows(5)->columnSpanFull(),
                        Textarea::make('home_about_cita')->label('Cita / frase destacada')->rows(2)->columnSpanFull(),
                    ]),

                Section::make('Inicio — Encabezados de secciones')
                    ->columns(2)
                    ->schema([
                        TextInput::make('home_accesos_eyebrow')->label('Accesos — antetítulo'),
                        TextInput::make('home_accesos_titulo')->label('Accesos — título'),
                        TextInput::make('home_stats_eyebrow')->label('Cifras — antetítulo'),
                        TextInput::make('home_stats_titulo')->label('Cifras — título'),
                        Textarea::make('home_stats_narrativa')->label('Cifras — narrativa')->rows(2)->columnSpanFull(),
                        TextInput::make('home_revista_eyebrow')->label('Revista — antetítulo'),
                        TextInput::make('home_revista_titulo')->label('Revista — título'),
                        TextInput::make('home_blog_eyebrow')->label('Blog — antetítulo'),
                        TextInput::make('home_blog_titulo')->label('Blog — título'),
                    ]),

                Section::make('Inicio — Banda animada (marquee)')
                    ->schema([
                        TextInput::make('home_marquee')->label('Términos de la banda')
                            ->helperText('Separa cada término con « · » (punto medio). Ej.: Derecho Civil · Derecho Penal'),
                    ]),

                Section::make('Plan de Estudios')
                    ->description('Los cursos por ciclo (malla) se editan en «Plan de Estudios (cursos)».')
                    ->columns(3)
                    ->schema([
                        TextInput::make('plan_grado')->label('Grado académico'),
                        TextInput::make('plan_titulo_prof')->label('Título profesional'),
                        TextInput::make('plan_modalidad')->label('Modalidad'),
                        Textarea::make('plan_intro')->label('Introducción de la malla (opcional)')->rows(2)->columnSpanFull(),
                        TextInput::make('plan_pdf_url')->label('Enlace al PDF de la malla')->rule(new SafeUrl)->columnSpanFull(),
                        TextInput::make('plan_sga_url')->label('Enlace al plan en línea (SGA)')->rule(new SafeUrl)->columnSpanFull(),
                    ]),

                Section::make('Footer, contacto y SEO')
                    ->columns(2)
                    ->schema([
                        TextInput::make('footer_marca')->label('Marca (nombre)'),
                        TextInput::make('lema')->label('Lema'),
                        Textarea::make('footer_descripcion')->label('Descripción')->rows(2)->columnSpanFull(),
                        TextInput::make('contacto_direccion')->label('Dirección'),
                        TextInput::make('contacto_telefono')->label('Teléfono'),
                        TextInput::make('contacto_email')->label('Email'),
                        TextInput::make('footer_cta_texto')->label('Botón CTA — texto'),
                        TextInput::make('footer_cta_url')->label('Botón CTA — URL')->rule(new SafeUrl),
                        TextInput::make('seo_title')->label('SEO — título de la pestaña')->columnSpanFull(),
                        Textarea::make('seo_description')->label('SEO — descripción (meta)')->rows(2)->columnSpanFull(),
                    ]),
            ]);
    }

    public function guardar(): void
    {
        foreach ($this->form->getState() as $clave => $valor) {
            Setting::set($clave, (string) $valor);
        }

        Notification::make()
            ->title('Ajustes guardados')
            ->success()
            ->send();
    }
}
