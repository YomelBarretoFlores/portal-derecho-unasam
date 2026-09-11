<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\Revista;
use App\Models\RevistaEnvio;
use App\Models\RevistaLineaInvestigacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Consulta pública de estado. Es la única vía que tiene el autor para seguir su
 * manuscrito, porque el portal no envía correos: la notificación va al panel.
 */
class RevistaConsultaEstadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_author_sees_the_editorial_phase_of_their_manuscript(): void
    {
        $envio = $this->submission('revision_pares');

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => $envio->codigo_seguimiento,
            'email_consulta' => 'autora@unasam.edu.pe',
        ])->assertRedirect(route('revista.envios.consulta'));

        $this->get(route('revista.envios.consulta'))
            ->assertOk()
            ->assertSee($envio->codigo_seguimiento)
            ->assertSee('Ensayo sobre pluralismo')
            ->assertSee('Revisión por pares')
            ->assertSee('Antropología jurídica')
            ->assertSee('no está en fase de corrección');
    }

    public function test_the_code_is_accepted_in_lowercase_and_with_stray_spaces(): void
    {
        $envio = $this->submission('recibido');

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => '  '.strtolower($envio->codigo_seguimiento).'  ',
            'email_consulta' => 'AUTORA@unasam.edu.pe',
        ])->assertRedirect(route('revista.envios.consulta'));

        $this->get(route('revista.envios.consulta'))->assertSee($envio->codigo_seguimiento);
    }

    public function test_an_observed_manuscript_invites_the_author_to_send_a_correction(): void
    {
        $envio = $this->submission('observado');

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => $envio->codigo_seguimiento,
            'email_consulta' => 'autora@unasam.edu.pe',
        ]);

        $this->get(route('revista.envios.consulta'))
            ->assertSee('puedes enviar tu versión corregida ahora', false)
            ->assertSee('Enviar corrección');
    }

    public function test_a_wrong_email_reveals_nothing_about_the_submission(): void
    {
        $envio = $this->submission('aceptado');

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => $envio->codigo_seguimiento,
            'email_consulta' => 'intruso@otra.edu.pe',
        ])->assertSessionHasErrors(['codigo_seguimiento'], null, 'consulta');

        $this->get(route('revista.envios.consulta'))
            ->assertDontSee('Ensayo sobre pluralismo')
            ->assertDontSee('Aceptado');
    }

    public function test_an_unknown_code_gets_the_same_generic_message_as_a_wrong_email(): void
    {
        $this->submission('recibido');

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => 'DYC-NOEXISTE1234',
            'email_consulta' => 'autora@unasam.edu.pe',
        ])->assertSessionHasErrors(['codigo_seguimiento' => 'No fue posible validar el código y el correo indicados.'], null, 'consulta');
    }

    public function test_internal_data_never_reaches_the_public_page(): void
    {
        $envio = $this->submission('observado');
        $envio->update(['observaciones_internas' => 'Revisor asignado: Dr. Confidencial. Rechazar si no corrige.']);

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => $envio->codigo_seguimiento,
            'email_consulta' => 'autora@unasam.edu.pe',
        ]);

        $this->get(route('revista.envios.consulta'))
            ->assertOk()
            ->assertDontSee('Dr. Confidencial')
            ->assertDontSee('Rechazar si no corrige')
            ->assertDontSee('87654321')          // documento de identidad
            ->assertDontSee('+51950061184')      // whatsapp
            ->assertDontSee('manuscrito.docx');  // ruta de archivo
    }

    public function test_the_lookup_is_rate_limited(): void
    {
        $this->submission('recibido');

        for ($i = 0; $i < 10; $i++) {
            $this->post(route('revista.envios.consulta.buscar'), [
                'codigo_seguimiento' => 'DYC-INTENTO'.$i,
                'email_consulta' => 'fuerza@bruta.edu.pe',
            ]);
        }

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => 'DYC-INTENTO11',
            'email_consulta' => 'fuerza@bruta.edu.pe',
        ])->assertStatus(429);
    }

    public function test_status_stays_available_when_new_submissions_are_closed(): void
    {
        $envio = $this->submission('revision_editorial');

        // Cerrar la recepción no debe dejar incomunicado a quien ya envió.
        config()->set('submissions.enabled', false);

        $this->post(route('revista.envios.consulta.buscar'), [
            'codigo_seguimiento' => $envio->codigo_seguimiento,
            'email_consulta' => 'autora@unasam.edu.pe',
        ])->assertRedirect(route('revista.envios.consulta'));

        $this->get(route('revista.envios.consulta'))->assertOk()->assertSee('Revisión editorial');
    }

    public function test_the_page_is_not_public_without_a_published_journal(): void
    {
        $this->get(route('revista.envios.consulta'))->assertNotFound();
    }

    private function submission(string $estado): RevistaEnvio
    {
        $revista = Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>P.</p>', 'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe',
            'estado_editorial' => EditorialStatus::Published->value,
        ]);

        $linea = RevistaLineaInvestigacion::query()->create([
            'revista_id' => $revista->id, 'nombre' => 'Antropología jurídica', 'activa' => true,
        ]);

        return RevistaEnvio::query()->create([
            'revista_id' => $revista->id, 'revista_linea_investigacion_id' => $linea->id,
            'codigo_seguimiento' => 'DYC-ABCDEFGHIJKL', 'nombres' => 'Autora QA',
            'documento_identidad' => '87654321', 'afiliacion' => 'UNASAM', 'ciudad' => 'Huaraz',
            'pais' => 'Perú', 'email_institucional' => 'autora@unasam.edu.pe',
            'whatsapp' => '+51950061184', 'tipo_contribucion' => 'ensayo',
            'titulo' => 'Ensayo sobre pluralismo', 'resumen' => 'Resumen.',
            'manuscrito_path' => 'revista-envios/dyc/manuscrito.docx', 'carta_path' => 'b.docx',
            'declaracion_path' => 'c.docx', 'constancia_estilo_path' => 'd.pdf',
            'estado' => $estado, 'consentimiento_at' => now(),
        ]);
    }
}
