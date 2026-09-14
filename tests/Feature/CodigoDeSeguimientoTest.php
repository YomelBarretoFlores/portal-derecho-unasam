<?php

namespace Tests\Feature;

use App\Models\Revista;
use App\Models\RevistaEnvio;
use App\Models\RevistaLineaInvestigacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * El código de seguimiento tiene que sobrevivir a una recarga.
 *
 * Es el único dato que el autor se lleva del envío: sin él no puede consultar
 * el estado ni mandar su versión corregida, y no hay forma de recuperarlo
 * —la consulta exige código Y correo, así que no basta con el correo—. Su
 * única salida sería escribir al equipo editorial.
 *
 * Antes viajaba en un mensaje flash, que dura exactamente una petición:
 * recargar la página de agradecimiento, volver atrás o un resbalón en el
 * móvil lo borraba para siempre. Ahora vive en la sesión hasta que el autor
 * confirma que lo ha guardado.
 */
class CodigoDeSeguimientoTest extends TestCase
{
    use RefreshDatabase;

    private function preparar(): RevistaLineaInvestigacion
    {
        Storage::fake('local');
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');

        $revista = Revista::query()->create([
            'nombre' => 'Derecho y Cultura, Revista Científica', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>', 'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe', 'normas_publicacion' => '<p>Normas oficiales.</p>',
            'contenido_politicas' => '<p>Recibe trabajos originales sometidos a revisión por pares doble ciego según las normas para autores.</p>',
            'contenido_indexacion' => '<p>Revista científica digital semestral de la Universidad Nacional Santiago Antúnez de Mayolo.</p>',
            'contenido_privacidad' => '<p>Protege la información de autores y colaboradores durante la gestión editorial de cada manuscrito recibido.</p>',
            'estado_editorial' => 'published',
        ]);

        return RevistaLineaInvestigacion::query()->create([
            'revista_id' => $revista->id, 'nombre' => 'Derecho penal', 'activa' => true,
        ]);
    }

    /** @return array<string, mixed> */
    private function datos(int $lineaId): array
    {
        return [
            'nombres' => 'Ana Ramírez Cruz',
            'documento_identidad' => '87654321',
            'afiliacion' => 'UNASAM',
            'ciudad' => 'Huaraz',
            'pais' => 'Perú',
            'email_institucional' => 'ana.ramirez@unasam.edu.pe',
            'whatsapp' => '+51950061184',
            'tipo_contribucion' => 'articulo_original',
            'revista_linea_investigacion_id' => $lineaId,
            'titulo' => 'El principio de legalidad en la jurisprudencia de Áncash',
            'resumen' => 'Resumen breve del manuscrito enviado para su evaluación editorial.',
            'consentimiento' => '1',
            'manuscrito' => UploadedFile::fake()->create('manuscrito.docx', 40, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'carta' => UploadedFile::fake()->create('carta.pdf', 20, 'application/pdf'),
            'declaracion' => UploadedFile::fake()->create('declaracion.pdf', 20, 'application/pdf'),
            'constancia_estilo' => UploadedFile::fake()->create('constancia.pdf', 20, 'application/pdf'),
        ];
    }

    public function test_the_tracking_code_survives_reloading_the_page(): void
    {
        $linea = $this->preparar();

        $this->post(route('revista.envios.store'), $this->datos($linea->id))
            ->assertRedirect(route('revista.envios'));

        $codigo = RevistaEnvio::query()->firstOrFail()->codigo_seguimiento;

        // La primera visita lo enseña, y la segunda también: eso es lo que un
        // mensaje flash no hacía.
        $this->get(route('revista.envios'))->assertOk()->assertSee($codigo);
        $this->get(route('revista.envios'))->assertOk()->assertSee($codigo);
        $this->get(route('revista.envios'))->assertOk()->assertSee($codigo);
    }

    public function test_the_author_can_dismiss_the_code_once_it_is_saved(): void
    {
        $linea = $this->preparar();
        $this->post(route('revista.envios.store'), $this->datos($linea->id));
        $codigo = RevistaEnvio::query()->firstOrFail()->codigo_seguimiento;

        $this->get(route('revista.envios'))->assertSee($codigo);

        $this->post(route('revista.envios.codigo-guardado'))
            ->assertRedirect(route('revista.envios'));

        $this->get(route('revista.envios'))->assertOk()->assertDontSee($codigo);
    }

    public function test_the_page_shows_no_code_to_someone_who_never_submitted(): void
    {
        $this->preparar();

        $this->get(route('revista.envios'))->assertOk()->assertDontSee('Guarda tu código de seguimiento');
    }
}
