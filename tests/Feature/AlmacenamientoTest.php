<?php

namespace Tests\Feature;

use App\Services\RevistaSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El comando de verificación y los motivos de cierre son lo que ETI y el equipo
 * editorial usarán para diagnosticar el almacenamiento en producción.
 */
class AlmacenamientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_command_reports_both_disks_and_succeeds_when_they_are_correct(): void
    {
        config()->set('submissions.disk', 'local');

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('Medios públicos')
            ->expectsOutputToContain('Manuscritos recibidos')
            ->expectsOutputToContain('PERSISTENCIA')
            ->assertSuccessful();
    }

    public function test_the_command_fails_when_manuscripts_would_sit_on_a_public_disk(): void
    {
        config()->set('submissions.disk', 'public');

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('NO deben ser descargables sin autenticación')
            ->assertFailed();
    }

    public function test_closed_reception_explains_which_switch_is_missing(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', false);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');

        $motivos = app(RevistaSubmissionService::class)->unavailableReasons();

        $this->assertCount(1, $motivos);
        $this->assertStringContainsString('SUBMISSIONS_PRIVACY_APPROVED', $motivos[0]);
    }

    public function test_a_public_disk_keeps_reception_closed_even_with_every_switch_on(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'public');

        $submissions = app(RevistaSubmissionService::class);

        $this->assertFalse($submissions->available());
        $this->assertStringContainsString('no es privado', implode(' ', $submissions->unavailableReasons()));
    }

    public function test_reception_is_open_when_everything_lines_up(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');

        $this->assertSame([], app(RevistaSubmissionService::class)->unavailableReasons());
        $this->assertTrue(app(RevistaSubmissionService::class)->available());
    }
}
