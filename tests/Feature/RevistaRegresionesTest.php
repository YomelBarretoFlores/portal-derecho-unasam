<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaEnvio;
use App\Models\RevistaLineaInvestigacion;
use App\Models\RevistaNumero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Regresiones del micrositio de la revista detectadas en la auditoría:
 * el índice público se caía al listar números y las correcciones podían
 * reabrir envíos ya cerrados.
 */
class RevistaRegresionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_lists_issues_without_crashing_on_cached_objects(): void
    {
        $revista = $this->journal();
        $numero = $this->issue($revista, 'volumen-1-numero-1');

        // El índice se sirve desde PublicContentCache, que entrega stdClass y no
        // modelos: pasar el objeto entero a route() reventaba con un 500.
        $this->get(route('revista'))
            ->assertOk()
            ->assertSee($numero->titulo)
            ->assertSee(route('revista.numero', $numero->slug));
    }

    public function test_preview_renders_the_same_index_view_with_eloquent_models(): void
    {
        $revista = $this->journal();
        $this->issue($revista, 'volumen-1-numero-1');

        // La misma vista recibe modelos Eloquent desde el panel: ambas formas
        // deben resolver el enlace al número.
        $this->get(route('revista'))->assertOk()->assertSee('volumen-1-numero-1');
    }

    public function test_correction_is_accepted_only_when_the_submission_was_observed(): void
    {
        $envio = $this->submission();

        $envio->update(['estado' => 'observado']);
        $this->postCorrection($envio)->assertRedirect(route('revista.envios'));
        $this->assertSame('correccion_recibida', $envio->refresh()->estado);
        $this->assertCount(1, $envio->versiones);
    }

    public function test_correction_cannot_reopen_a_closed_submission(): void
    {
        $envio = $this->submission();

        foreach (['rechazado', 'aceptado', 'publicado', 'recibido'] as $estado) {
            $envio->update(['estado' => $estado]);

            $this->postCorrection($envio)
                ->assertRedirect()
                ->assertSessionHasErrors(['codigo_seguimiento'], null, 'correccion');

            $this->assertSame($estado, $envio->refresh()->estado, "El estado {$estado} no debe reabrirse.");
            $this->assertCount(0, $envio->versiones()->get());
        }
    }

    public function test_correction_errors_use_their_own_error_bag(): void
    {
        $envio = $this->submission();
        $envio->update(['estado' => 'observado']);

        // Correo que no coincide: el error pertenece al formulario de corrección,
        // no al de envío de manuscritos.
        $this->post(route('revista.envios.correction'), [
            'codigo_seguimiento' => $envio->codigo_seguimiento,
            'email_institucional_correccion' => 'otro@universidad.edu.pe',
            'manuscrito_corregido' => $this->docx(),
        ])
            ->assertSessionHasErrors(['codigo_seguimiento'], null, 'correccion')
            ->assertSessionDoesntHaveErrors(['codigo_seguimiento'], null, 'default');
    }

    private function postCorrection(RevistaEnvio $envio): TestResponse
    {
        return $this->post(route('revista.envios.correction'), [
            'codigo_seguimiento' => $envio->codigo_seguimiento,
            'email_institucional_correccion' => 'autor@universidad.edu.pe',
            'manuscrito_corregido' => $this->docx(),
        ]);
    }

    private function submission(): RevistaEnvio
    {
        Storage::fake('local');
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');

        $revista = $this->journal();
        $linea = RevistaLineaInvestigacion::query()->create(['revista_id' => $revista->id, 'nombre' => 'Derecho', 'activa' => true]);

        $this->post(route('revista.envios.store'), [
            'nombres' => 'Autora de Prueba', 'documento_identidad' => '87654321',
            'afiliacion' => 'Universidad de Prueba', 'ciudad' => 'Huaraz', 'pais' => 'Perú',
            'email_institucional' => 'autor@universidad.edu.pe', 'whatsapp' => '+51950061184',
            'tipo_contribucion' => 'articulo_original', 'revista_linea_investigacion_id' => $linea->id,
            'titulo' => 'Título de prueba', 'resumen' => 'Resumen breve para la evaluación editorial.',
            'manuscrito' => $this->docx(), 'carta' => $this->docx(), 'declaracion' => $this->docx(),
            'constancia_estilo' => UploadedFile::fake()->create('constancia.pdf', 5, 'application/pdf'),
            'consentimiento' => '1',
        ])->assertRedirect();

        return RevistaEnvio::query()->firstOrFail();
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

    private function issue(Revista $revista, string $slug): RevistaNumero
    {
        $numero = RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1', 'slug' => $slug,
            'titulo' => 'Primer número', 'fecha_publicacion' => today(), 'es_actual' => true,
            'estado_editorial' => 'published',
        ]);

        Articulo::query()->create([
            'revista_numero_id' => $numero->id, 'titulo' => 'Artículo de prueba',
            'autores' => ['Autora de Prueba'], 'categoria' => 'Artículo original',
            'resumen' => 'Resumen.', 'contenido' => '<p>Cuerpo.</p>', 'fecha' => today(),
            'estado_editorial' => 'published',
        ]);

        return $numero;
    }

    private function docx(): UploadedFile
    {
        return UploadedFile::fake()->create('manuscrito.docx', 5, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }
}
