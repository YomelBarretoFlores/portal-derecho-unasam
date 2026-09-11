<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Filament\Resources\RevistaEnvios\Pages\EditRevistaEnvio;
use App\Filament\Resources\RevistaEnvios\RelationManagers\VersionesRelationManager;
use App\Filament\Resources\RevistaNumeros\Pages\CreateRevistaNumero;
use App\Filament\Resources\RevistaNumeros\Pages\EditRevistaNumero;
use App\Filament\Resources\RevistaNumeros\RelationManagers\ArticulosRelationManager;
use App\Filament\Widgets\RevistaContenidoOverview;
use App\Filament\Widgets\RevistaEnviosOverview;
use App\Filament\Widgets\RevistaEnviosRecientes;
use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaEnvio;
use App\Models\RevistaNumero;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Cobertura del panel: widgets del dashboard, relation managers y las guardas
 * que conectan la gestión editorial con lo que se publica.
 */
class RevistaPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_dashboard_widgets_summarise_submissions_and_content(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
        $revista = $this->journal();
        $numero = $this->issue($revista);
        $this->article($numero, publicado: true);
        $this->article($numero, publicado: false);

        $this->submission($revista, 'recibido');
        $this->submission($revista, 'observado');
        $this->submission($revista, 'rechazado');

        Livewire::test(RevistaEnviosOverview::class)
            ->assertOk()
            ->assertSee('Requieren atención')
            ->assertSee('En proceso editorial');

        Livewire::test(RevistaContenidoOverview::class)
            ->assertOk()
            ->assertSee('Números publicados')
            ->assertSee('1 / 1')   // un número, publicado
            ->assertSee('1 / 2');  // dos artículos, uno publicado

        Livewire::test(RevistaEnviosRecientes::class)
            ->assertOk()
            ->assertSee('Últimos envíos recibidos');
    }

    public function test_dashboard_widgets_are_hidden_from_users_without_content_permissions(): void
    {
        $this->actingAs(User::factory()->create(['role' => null, 'is_admin' => false]));

        $this->assertFalse(RevistaEnviosOverview::canView());
        $this->assertFalse(RevistaContenidoOverview::canView());
        $this->assertFalse(RevistaEnviosRecientes::canView());
    }

    public function test_articles_are_managed_from_their_issue(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
        $revista = $this->journal();
        $numero = $this->issue($revista);
        $articulo = $this->article($numero, publicado: true);

        Livewire::test(ArticulosRelationManager::class, [
            'ownerRecord' => $numero,
            'pageClass' => EditRevistaNumero::class,
        ])->assertOk()->assertSee($articulo->titulo);
    }

    public function test_corrections_are_listed_from_their_submission(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
        $revista = $this->journal();
        $envio = $this->submission($revista, 'observado');
        $envio->versiones()->create(['numero' => 1, 'archivo_path' => 'c.docx', 'nota_autor' => 'Nota de la autora', 'recibida_at' => now()]);

        Livewire::test(VersionesRelationManager::class, [
            'ownerRecord' => $envio,
            'pageClass' => EditRevistaEnvio::class,
        ])->assertOk()->assertSee('Nota de la autora');
    }

    public function test_an_issue_cannot_take_the_slug_of_a_static_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
        $revista = $this->journal();

        $this->assertContains('archivos', RevistaNumero::slugsReservados());
        $this->assertContains('envios', RevistaNumero::slugsReservados());

        Livewire::test(CreateRevistaNumero::class)
            ->fillForm([
                'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1',
                'titulo' => 'Número en conflicto', 'slug' => 'archivos',
                'fecha_publicacion' => today(), 'estado_editorial' => 'draft',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_child_forms_preselect_the_only_journal(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
        $revista = $this->journal();

        Livewire::test(CreateRevistaNumero::class)->assertFormSet(['revista_id' => $revista->id]);
    }

    private function journal(): Revista
    {
        return Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>', 'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe',
            'estado_editorial' => EditorialStatus::Published->value,
        ]);
    }

    private function issue(Revista $revista): RevistaNumero
    {
        return RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1',
            'slug' => 'volumen-1-numero-1', 'titulo' => 'Primer número',
            'fecha_publicacion' => today(), 'es_actual' => true, 'estado_editorial' => 'published',
        ]);
    }

    private function article(RevistaNumero $numero, bool $publicado): Articulo
    {
        return Articulo::query()->create([
            'revista_numero_id' => $numero->id,
            'titulo' => 'Artículo '.($publicado ? 'publicado' : 'en borrador').' '.uniqid(),
            'autores' => ['Autora'], 'categoria' => 'Artículo original',
            'resumen' => 'Resumen.', 'contenido' => '<p>Cuerpo.</p>', 'fecha' => today(),
            'estado_editorial' => $publicado ? 'published' : 'draft',
        ]);
    }

    private function submission(Revista $revista, string $estado): RevistaEnvio
    {
        return RevistaEnvio::query()->create([
            'revista_id' => $revista->id, 'codigo_seguimiento' => 'DYC-'.strtoupper(substr(md5($estado.uniqid()), 0, 12)),
            'nombres' => 'Autora QA', 'documento_identidad' => '12345678', 'afiliacion' => 'UNASAM',
            'ciudad' => 'Huaraz', 'pais' => 'Perú', 'email_institucional' => 'autora@unasam.edu.pe',
            'whatsapp' => '+51999999999', 'tipo_contribucion' => 'ensayo', 'titulo' => 'Ensayo QA',
            'resumen' => 'Resumen', 'manuscrito_path' => 'a.docx', 'carta_path' => 'b.docx',
            'declaracion_path' => 'c.docx', 'constancia_estilo_path' => 'd.pdf',
            'estado' => $estado, 'consentimiento_at' => now(),
        ]);
    }
}
