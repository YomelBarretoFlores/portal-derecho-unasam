<?php

namespace Tests\Feature;

use App\Filament\Resources\Revistas\Pages\EditRevista;
use App\Models\Revista;
use App\Models\Setting;
use App\Models\User;
use App\Services\RevistaSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El panel y el fichero de entorno no pueden decir cosas distintas.
 *
 * Ahora hay dos sitios donde se decide si se reciben manuscritos, así que hay
 * que demostrar que no se contradicen. El caso peligroso es el interruptor
 * bloqueado: Filament no envía los campos deshabilitados al guardar, de modo
 * que al guardar cualquier otro cambio de la ficha el ajuste se leería como
 * vacío y la recepción se cerraría sola, sin que nadie lo pidiera.
 */
class RecepcionSinContradiccionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function revista(): Revista
    {
        return Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'estado_editorial' => 'draft',
        ]);
    }

    public function test_saving_the_journal_never_closes_reception_by_itself(): void
    {
        // Servidor NO listo: el interruptor sale bloqueado y Filament no lo
        // manda al guardar. El ajuste tiene que quedarse como estaba.
        config()->set('submissions.storage_persistent', false);
        Setting::set('revista_recepcion_abierta', '1');
        $revista = $this->revista();

        Livewire::actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]))
            ->test(EditRevista::class, ['record' => $revista->getKey()])
            ->fillForm(['nombre_corto' => 'DYC'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('1', (string) Setting::get('revista_recepcion_abierta'),
            'Guardar la ficha cerró la recepción sin que nadie lo pidiera.');
    }

    public function test_the_panel_and_the_environment_agree_on_the_reason(): void
    {
        // Con el servidor listo, el único motivo posible es el editorial, y el
        // texto tiene que señalar al panel y no a una variable de entorno.
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');
        Setting::set('revista_recepcion_abierta', '0');

        $motivos = app(RevistaSubmissionService::class)->unavailableReasons();

        $this->assertCount(1, $motivos);
        $this->assertStringContainsString('panel', $motivos[0]);
    }

    public function test_technical_reasons_come_first_so_the_dashboard_shows_the_real_blocker(): void
    {
        // El escritorio enseña solo el primer motivo. Si el editorial fuese
        // antes, diría «cerrada desde el panel» cuando el problema real es que
        // el servidor no puede guardar nada.
        config()->set('submissions.storage_persistent', false);
        Setting::set('revista_recepcion_abierta', '0');

        $motivos = app(RevistaSubmissionService::class)->unavailableReasons();

        $this->assertStringNotContainsString('panel', $motivos[0]);
    }
}
