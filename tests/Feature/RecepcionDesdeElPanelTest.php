<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\RevistaSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Abrir y cerrar la convocatoria es del equipo editorial; poder guardar los
 * manuscritos es del servidor.
 *
 * Los cinco requisitos técnicos —almacenamiento privado, que sobreviva a un
 * despliegue, la declaración de privacidad— viven en el fichero de entorno
 * porque nadie los puede afirmar desde un panel sin mentir, y mentir ahí
 * significa perder trabajos inéditos de autores.
 *
 * «¿Estamos recibiendo ahora mismo?» es otra cosa: cambia cada vez que se abre
 * o se cierra una convocatoria, y es del panel. Estos tests fijan que el
 * interruptor del panel SUMA a los técnicos y no los sustituye.
 */
class RecepcionDesdeElPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function servidorListo(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');
    }

    public function test_the_panel_can_close_submissions_on_a_server_that_allows_them(): void
    {
        $this->servidorListo();
        $envios = app(RevistaSubmissionService::class);
        $this->assertTrue($envios->available());

        Setting::set('revista_recepcion_abierta', '0');

        $this->assertFalse(app(RevistaSubmissionService::class)->available());
    }

    public function test_the_panel_cannot_open_submissions_the_server_cannot_hold(): void
    {
        // Este es el límite que justifica que los otros cinco no estén en el
        // panel: por mucho que se pulse, no se abre.
        config()->set('submissions.storage_persistent', false);
        Setting::set('revista_recepcion_abierta', '1');

        $this->assertFalse(app(RevistaSubmissionService::class)->available());
    }

    public function test_the_technical_reasons_are_reported_apart(): void
    {
        // El panel los enseña para explicar POR QUÉ el interruptor está
        // bloqueado, en vez de limitarse a no dejarse pulsar.
        config()->set('submissions.storage_persistent', false);
        Setting::set('revista_recepcion_abierta', '1');

        $tecnicos = app(RevistaSubmissionService::class)->motivosTecnicosPendientes();

        $this->assertNotEmpty($tecnicos);
        foreach ($tecnicos as $motivo) {
            $this->assertStringNotContainsString('desde el panel', $motivo);
        }
    }

    public function test_upgrading_does_not_close_a_journal_that_was_already_open(): void
    {
        // Si el ajuste empezara en «cerrado», instalar esta versión cortaría la
        // recepción en un portal que la tenía abierta, sin que nadie lo pidiera.
        $this->servidorListo();
        $this->assertNull(Setting::query()->where('clave', 'revista_recepcion_abierta')->first());

        $this->assertTrue(app(RevistaSubmissionService::class)->available());
    }
}
