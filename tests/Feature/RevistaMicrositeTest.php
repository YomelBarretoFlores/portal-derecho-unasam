<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\Revista;
use App\Models\RevistaContacto;
use App\Models\RevistaDocumento;
use App\Models\RevistaEnvio;
use App\Models\RevistaLineaInvestigacion;
use App\Models\RevistaMiembro;
use App\Models\RevistaNumero;
use App\Models\User;
use Database\Seeders\RevistaContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RevistaMicrositeTest extends TestCase
{
    use RefreshDatabase;

    public function test_microsite_routes_navigation_and_placeholders_are_public(): void
    {
        config()->set('submissions.enabled', false);
        $revista = $this->journal();
        RevistaDocumento::query()->create(['revista_id' => $revista->id, 'categoria' => 'norma', 'titulo' => 'Normas de publicación', 'url' => '/docs/revista/normas-publicacion-v1.pdf', 'visible' => true, 'estado_editorial' => 'published']);

        $this->get(route('revista.actual'))->assertOk()->assertSee('Derecho y Cultura inicia su colección editorial')->assertSee('PDF');
        $this->get(route('revista.archivos'))->assertOk()->assertSee('Aún no hay ediciones anteriores');
        $this->get(route('revista.politicas'))->assertOk()->assertSee('revisión por pares doble ciego');
        $this->get(route('revista.avisos'))->assertOk()->assertSee('Sin avisos vigentes');
        $this->get(route('revista.indexacion'))->assertOk()->assertSee('revista científica digital de periodicidad semestral')->assertDontSee('ISSN en línea en proceso');
        $this->get(route('revista.privacidad'))->assertOk()->assertSee('no se recopilan datos personales mediante formularios públicos');
        $this->get(route('revista.normas'))->assertOk()->assertSee('Garamond 12')->assertSee('normas-publicacion-v1.pdf');
        $this->get(route('revista.envios'))->assertOk()->assertSee('Actual')->assertSee('Acerca de')->assertSee('Recepción en línea cerrada');
    }

    public function test_editorial_and_scientific_committees_are_separated(): void
    {
        $revista = $this->journal();
        RevistaMiembro::query()->create(['revista_id' => $revista->id, 'grupo' => 'comite_editorial', 'nombre' => 'Persona Editorial', 'activo' => true]);
        RevistaMiembro::query()->create(['revista_id' => $revista->id, 'grupo' => 'consejo_cientifico', 'nombre' => 'Persona Científica', 'activo' => true]);

        $this->get(route('revista.comite-editorial'))->assertOk()->assertSee('Persona Editorial')->assertDontSee('Persona Científica');
        $this->get(route('revista.comite-cientifico'))->assertOk()->assertSee('Persona Científica')->assertDontSee('Persona Editorial');
    }

    public function test_only_one_issue_can_be_current_and_archives_keep_other_issues(): void
    {
        $revista = $this->journal();
        $first = $this->issue($revista, 'uno', true);
        $second = $this->issue($revista, 'dos', true);

        $this->assertFalse($first->refresh()->es_actual);
        $this->assertTrue($second->refresh()->es_actual);
        $this->get(route('revista.actual'))->assertSee('Número dos')->assertDontSee('Número uno');
        $this->get(route('revista.archivos'))->assertSee('Número uno')->assertDontSee('Número dos');
    }

    public function test_submission_requires_all_three_safety_switches(): void
    {
        $this->journal();
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', false);
        config()->set('submissions.storage_persistent', true);
        $this->post(route('revista.envios.store'))->assertStatus(503);
    }

    public function test_submission_is_encrypted_private_and_accepts_a_correction(): void
    {
        Storage::fake('local');
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');
        $revista = $this->journal();
        $linea = RevistaLineaInvestigacion::query()->create(['revista_id' => $revista->id, 'nombre' => 'Derecho', 'activa' => true]);
        $editor = User::factory()->create(['role' => User::ROLE_EDITOR]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->post(route('revista.envios.store'), $this->submissionData($linea->id));
        $response->assertRedirect(route('revista.envios'));
        $envio = RevistaEnvio::query()->firstOrFail();
        $this->assertSame('87654321', $envio->documento_identidad);
        $this->assertSame('+51950061184', $envio->whatsapp);
        $this->assertNotSame('87654321', DB::table('revista_envios')->value('documento_identidad'));
        Storage::disk('local')->assertExists($envio->manuscrito_path);
        $this->assertMatchesRegularExpression('/^DYC-[A-Z0-9]{12}$/', $envio->codigo_seguimiento);
        $this->assertDatabaseCount('notifications', 2);
        $this->assertSame('Nuevo manuscrito recibido', $editor->notifications()->firstOrFail()->data['title']);
        $this->assertSame('Nuevo manuscrito recibido', $superAdmin->notifications()->firstOrFail()->data['title']);
        $editorNotification = $editor->notifications()->firstOrFail();
        $notificationPayload = json_encode($editorNotification->data);
        $this->assertStringNotContainsString('87654321', $notificationPayload);
        $this->assertStringNotContainsString('+51950061184', $notificationPayload);
        $this->assertStringContainsString('/admin/revista-envios/'.$envio->id.'/edit', $editorNotification->data['actions'][0]['url']);

        $this->post(route('revista.envios.correction'), [
            'codigo_seguimiento' => strtolower($envio->codigo_seguimiento),
            'email_institucional_correccion' => 'autor@universidad.edu.pe',
            'manuscrito_corregido' => $this->docx('corregido.docx'),
        ])->assertRedirect(route('revista.envios'));
        $this->assertSame('correccion_recibida', $envio->refresh()->estado);
        $this->assertCount(1, $envio->versiones);
        $this->assertDatabaseCount('notifications', 4);
        $this->assertContains('Nueva corrección recibida', $editor->notifications()->get()->pluck('data.title')->all());

        $unauthorized = User::factory()->create(['role' => null, 'is_admin' => false]);
        $this->actingAs($unauthorized)->get(route('revista.envios.admin.download', [$envio, 'manuscrito']))->assertForbidden();
        $this->actingAs($editor)->get(route('revista.envios.admin.download', [$envio, 'manuscrito']))->assertOk();
    }

    public function test_submission_rejects_a_public_storage_disk(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'public');
        $this->journal();

        $this->get(route('revista.envios'))
            ->assertOk()
            ->assertSee('Recepción en línea cerrada');
        $this->post(route('revista.envios.store'))->assertStatus(503);
    }

    public function test_submission_rejects_long_abstract_too_many_coauthors_and_invalid_files(): void
    {
        Storage::fake('local');
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        $revista = $this->journal();
        $linea = RevistaLineaInvestigacion::query()->create(['revista_id' => $revista->id, 'nombre' => 'Derecho', 'activa' => true]);
        $data = $this->submissionData($linea->id);
        $data['resumen'] = implode(' ', array_fill(0, 201, 'palabra'));
        $data['coautores'] = ['Uno', 'Dos', 'Tres', 'Cuatro'];
        $data['email_institucional'] = 'autor@gmail.com';
        $data['manuscrito'] = UploadedFile::fake()->create('manuscrito.pdf', 5, 'application/pdf');

        $this->from(route('revista.envios'))->post(route('revista.envios.store'), $data)
            ->assertRedirect(route('revista.envios'))
            ->assertSessionHasErrors(['email_institucional', 'resumen', 'coautores', 'manuscrito']);
        $this->assertDatabaseCount('revista_envios', 0);
    }

    public function test_submission_endpoint_is_limited_to_five_attempts_per_hour_and_ip(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        $this->journal();
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('revista.envios.store'))->assertSessionHasErrors('nombres');
        }
        $this->post(route('revista.envios.store'))->assertTooManyRequests();
    }

    public function test_public_documents_exist_with_expected_mime_types(): void
    {
        $files = [
            'normas-publicacion-v1.pdf' => 'application/pdf',
            'carta-presentacion-v1.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'declaracion-originalidad-cesion-v1.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'plantilla-editorial-v1.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'resolucion-creacion-v1.pdf' => 'application/pdf',
        ];
        foreach ($files as $name => $mime) {
            $path = public_path('docs/revista/'.$name);
            $this->assertFileExists($path);
            $this->assertSame($mime, mime_content_type($path));
        }
    }

    public function test_versioned_document_urls_do_not_require_media_library_when_uploads_are_disabled(): void
    {
        config()->set('media.uploads_enabled', false);
        $revista = $this->journal();
        RevistaDocumento::query()->create([
            'revista_id' => $revista->id,
            'categoria' => 'norma',
            'titulo' => 'Normas de publicación',
            'url' => '/docs/revista/normas-publicacion-v1.pdf',
            'visible' => true,
            'estado_editorial' => 'published',
        ]);

        $this->get(route('revista.normas'))
            ->assertOk()
            ->assertSee('/docs/revista/normas-publicacion-v1.pdf', false);
    }

    public function test_initial_content_import_is_idempotent_and_preserves_existing_details(): void
    {
        $revista = $this->journal();
        RevistaMiembro::query()->create(['revista_id' => $revista->id, 'grupo' => 'editores', 'nombre' => 'Katherine Mónica Castro Menacho', 'email' => 'detalle-existente@unasam.edu.pe', 'activo' => true]);

        app(RevistaContentSeeder::class)->run();
        $counts = [RevistaMiembro::count(), RevistaContacto::count(), RevistaDocumento::count(), RevistaLineaInvestigacion::count()];
        app(RevistaContentSeeder::class)->run();

        $this->assertSame($counts, [RevistaMiembro::count(), RevistaContacto::count(), RevistaDocumento::count(), RevistaLineaInvestigacion::count()]);
        $this->assertSame('detalle-existente@unasam.edu.pe', RevistaMiembro::query()->where('nombre', 'Katherine Mónica Castro Menacho')->value('email'));
    }

    public function test_initial_content_import_keeps_the_journal_public_when_a_legacy_status_disagrees(): void
    {
        $revista = $this->journal();
        DB::table('revistas')->where('id', $revista->id)->update([
            'estado_editorial' => EditorialStatus::Verified->value,
            'activo' => true,
            'publicado' => true,
        ]);

        app(RevistaContentSeeder::class)->run();

        $revista->refresh();
        $this->assertSame(EditorialStatus::Published->value, $revista->estado_editorial);
        $this->assertTrue($revista->activo);
        $this->assertTrue($revista->publicado);
    }

    private function journal(): Revista
    {
        return Revista::query()->create([
            'nombre' => 'Derecho y Cultura, Revista Científica de Derecho y Antropología Jurídica', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>', 'unidad_responsable' => 'Unidad de Investigación', 'resolucion_numero' => '063-2026',
            'resolucion_fecha' => '2026-07-06', 'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe', 'normas_publicacion' => '<p>Normas oficiales.</p>',
            'contenido_politicas' => '<p>Derecho y Cultura recibe trabajos originales vinculados al Derecho y la Antropología Jurídica. Los manuscritos se someten a verificación editorial y revisión por pares doble ciego de acuerdo con las normas para autores.</p>',
            'contenido_indexacion' => '<p>Derecho y Cultura es una revista científica digital de periodicidad semestral de la Universidad Nacional Santiago Antúnez de Mayolo.</p>', 'contenido_privacidad' => '<p>Derecho y Cultura protege la información de autores y colaboradores durante la gestión editorial. Actualmente no se recopilan datos personales mediante formularios públicos.</p>',
            'estado_editorial' => EditorialStatus::Published->value,
        ]);
    }

    private function issue(Revista $revista, string $slug, bool $current): RevistaNumero
    {
        return RevistaNumero::query()->create(['revista_id' => $revista->id, 'volumen' => '1', 'numero' => $slug, 'slug' => $slug, 'titulo' => 'Número '.$slug, 'fecha_publicacion' => today(), 'es_actual' => $current, 'estado_editorial' => 'published']);
    }

    /** @return array<string, mixed> */
    private function submissionData(int $linea): array
    {
        return ['nombres' => 'Autora de Prueba', 'documento_identidad' => '87654321', 'afiliacion' => 'Universidad de Prueba', 'ciudad' => 'Huaraz', 'pais' => 'Perú', 'email_institucional' => 'autor@universidad.edu.pe', 'whatsapp' => '+51950061184', 'orcid' => '0000-0002-1825-0097', 'tipo_contribucion' => 'articulo_original', 'revista_linea_investigacion_id' => $linea, 'titulo' => 'Título de prueba', 'resumen' => 'Resumen breve para la evaluación editorial.', 'coautores' => ['Coautor Uno'], 'manuscrito' => $this->docx('manuscrito.docx'), 'carta' => $this->docx('carta.docx'), 'declaracion' => $this->docx('declaracion.docx'), 'constancia_estilo' => UploadedFile::fake()->create('constancia.pdf', 5, 'application/pdf'), 'consentimiento' => '1'];
    }

    private function docx(string $name): UploadedFile
    {
        return UploadedFile::fake()->create($name, 5, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }
}
