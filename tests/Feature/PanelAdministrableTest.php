<?php

namespace Tests\Feature;

use App\Filament\Pages\AjustesSitio;
use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaContacto;
use App\Models\RevistaDocumento;
use App\Models\RevistaMiembro;
use App\Models\RevistaNumero;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Verifica que TODO el contenido del portal sea administrable: que cada modelo
 * tenga superficie en el panel, que cada pantalla cargue de verdad y que la
 * autorización esté completa (el panel usa strictAuthorization, así que un
 * modelo sin política queda inaccesible en silencio).
 */
class PanelAdministrableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    /** @return array<int, class-string> */
    private function recursos(): array
    {
        return collect(glob(app_path('Filament/Resources/*/*Resource.php')))
            ->map(fn (string $f): string => 'App\\Filament\\Resources\\'.basename(dirname($f)).'\\'.basename($f, '.php'))
            ->filter(fn (string $c): bool => class_exists($c))
            ->values()->all();
    }

    public function test_every_content_model_is_reachable_from_the_panel(): void
    {
        $administrados = collect($this->recursos())->map(fn (string $r): string => $r::getModel());

        $huerfanos = collect(glob(app_path('Models/*.php')))
            ->map(fn (string $f): string => 'App\\Models\\'.basename($f, '.php'))
            ->filter(fn (string $c): bool => class_exists($c) && is_subclass_of($c, Model::class))
            // Setting se administra desde la página «Ajustes del sitio», no desde un recurso.
            ->reject(fn (string $c): bool => $c === Setting::class)
            ->reject(fn (string $c): bool => $administrados->contains($c))
            ->map(fn (string $c): string => class_basename($c))
            ->values()->all();

        $this->assertSame([], $huerfanos, 'Modelos sin forma de administrarse: '.implode(', ', $huerfanos));
    }

    public function test_every_model_has_an_authorization_policy(): void
    {
        // Sin política, strictAuthorization deniega todo y el recurso desaparece
        // del panel sin ningún aviso.
        $sinPolitica = collect(glob(app_path('Models/*.php')))
            ->map(fn (string $f): string => 'App\\Models\\'.basename($f, '.php'))
            ->filter(fn (string $c): bool => class_exists($c) && is_subclass_of($c, Model::class))
            ->reject(fn (string $c): bool => Gate::getPolicyFor($c) !== null)
            ->map(fn (string $c): string => class_basename($c))
            ->values()->all();

        $this->assertSame([], $sinPolitica, 'Modelos sin política: '.implode(', ', $sinPolitica));
    }

    public function test_every_resource_listing_loads(): void
    {
        foreach ($this->recursos() as $recurso) {
            $paginas = $recurso::getPages();
            if (! isset($paginas['index'])) {
                continue;
            }

            Livewire::test($paginas['index']->getPage())
                ->assertOk();
        }
    }

    public function test_every_resource_creation_form_loads(): void
    {
        foreach ($this->recursos() as $recurso) {
            $paginas = $recurso::getPages();
            if (! isset($paginas['create']) || ! $recurso::canCreate()) {
                continue;
            }

            // Montar la página construye el esquema completo del formulario:
            // un campo mal declarado revienta aquí.
            Livewire::test($paginas['create']->getPage())
                ->assertOk();
        }
    }

    public function test_every_ordering_field_that_drives_the_public_site_is_editable(): void
    {
        // «orden» decide el orden de los artículos dentro de su número y, en
        // PublicContentCache::home(), cuáles se destacan en la portada. Si no se
        // puede editar, esa decisión editorial queda fuera del alcance del panel.
        $ordenables = [
            Articulo::class => 'app/Filament/Resources/Articulos',
            RevistaNumero::class => 'app/Filament/Resources/RevistaNumeros',
            RevistaMiembro::class => 'app/Filament/Resources/RevistaMiembros',
            RevistaDocumento::class => 'app/Filament/Resources/RevistaDocumentos',
            RevistaContacto::class => 'app/Filament/Resources/RevistaContactos',
        ];

        $sinCampo = [];
        foreach ($ordenables as $modelo => $dir) {
            $fuente = collect(glob(base_path($dir).'/*.php'))
                ->merge(glob(base_path($dir).'/*/*.php'))
                ->map(fn (string $f): string => (string) file_get_contents($f))
                ->implode("\n");

            if (! str_contains($fuente, "make('orden')")) {
                $sinCampo[] = class_basename($modelo);
            }
        }

        $this->assertSame([], $sinCampo, 'Sin campo de orden en el panel: '.implode(', ', $sinCampo));
    }

    public function test_article_order_reaches_the_public_site(): void
    {
        $revista = Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'DyC',
            'presentacion' => '<p>P.</p>', 'unidad_responsable' => 'U.',
            'resolucion_numero' => '063', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'R.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'r@unasam.edu.pe', 'estado_editorial' => 'published',
        ]);
        $numero = RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1',
            'slug' => 'volumen-1-numero-1', 'titulo' => 'Número', 'fecha_publicacion' => today(),
            'es_actual' => true, 'estado_editorial' => 'published',
        ]);

        foreach ([['Primero', 2], ['Segundo', 1]] as [$titulo, $orden]) {
            Articulo::query()->create([
                'revista_numero_id' => $numero->id, 'titulo' => $titulo,
                'slug' => Str::slug($titulo), 'autores' => ['A'],
                'categoria' => 'Ensayo', 'resumen' => 'R.', 'contenido' => '<p>C.</p>',
                'fecha' => today(), 'orden' => $orden, 'estado_editorial' => 'published',
            ]);
        }

        // El que lleva orden menor debe aparecer antes, pese a compartir fecha.
        $html = $this->get(route('revista.numero', $numero->slug))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($html, 'Primero'),
            strpos($html, 'Segundo'),
            'El campo «orden» no está gobernando el orden público de los artículos.',
        );
    }

    public function test_the_site_settings_page_loads(): void
    {
        Livewire::test(AjustesSitio::class)->assertOk();
    }

    public function test_the_dashboard_loads_with_its_widgets(): void
    {
        Livewire::test(Dashboard::class)->assertOk();
    }
}
