<?php

namespace Tests\Feature;

use App\Support\LimiteDeCargaTemporal;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Tests\TestCase;

/**
 * El almacén temporal de Livewire no puede ser más estrecho que el panel.
 *
 * Se descubrió comparando lo que el panel anuncia con lo que Livewire acepta:
 * 20 MB prometidos para un PDF contra 12 MB de tope real. En esa franja el
 * archivo se rechaza con un mensaje genérico antes de llegar a ninguna regla
 * nuestra, y quien lo sube no tiene forma de saber por qué.
 */
class LimiteDeCargaTemporalTest extends TestCase
{
    public function test_the_temporary_store_accepts_everything_the_panel_promises(): void
    {
        $reglas = FileUploadConfiguration::rules();

        $tope = $this->topeDe($reglas);
        $prometido = LimiteDeCargaTemporal::maximoEnKb();

        $this->assertNotNull($tope, 'Las reglas de carga temporal no declaran ningún máximo.');
        $this->assertGreaterThanOrEqual(
            $prometido,
            $tope,
            "Livewire corta en {$tope} KB y el panel promete {$prometido} KB: ".
            'los archivos entre medias se rechazan sin explicación.',
        );
    }

    public function test_the_cap_follows_the_configured_limits(): void
    {
        config()->set('media.max_pdf_kb', 40960);
        config()->set('media.max_image_kb', 1024);
        config()->set('submissions.manuscript_max_kb', 1024);
        config()->set('submissions.attachment_max_kb', 1024);

        LimiteDeCargaTemporal::aplicar();

        $this->assertSame(40960, $this->topeDe(FileUploadConfiguration::rules()));
    }

    public function test_it_never_produces_a_cap_of_zero(): void
    {
        // Con las cuatro variables vacías, un «max:0» rechazaría cualquier
        // archivo y el panel parecería roto sin que nada lo explique.
        foreach (['media.max_pdf_kb', 'media.max_image_kb', 'submissions.manuscript_max_kb', 'submissions.attachment_max_kb'] as $clave) {
            config()->set($clave, 0);
        }

        LimiteDeCargaTemporal::aplicar();

        $this->assertGreaterThan(0, $this->topeDe(FileUploadConfiguration::rules()));
    }

    /** @param  array<int, string>  $reglas */
    private function topeDe(array $reglas): ?int
    {
        foreach ($reglas as $regla) {
            if (is_string($regla) && str_starts_with($regla, 'max:')) {
                return (int) substr($regla, 4);
            }
        }

        return null;
    }
}
