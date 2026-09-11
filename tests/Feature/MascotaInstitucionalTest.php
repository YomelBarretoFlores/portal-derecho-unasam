<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * La mascota aparece en tres sitios contados y en ninguno más. Eso no es una
 * preferencia: PRODUCT.md pide «confianza y respeto, no entusiasmo», y una
 * ilustración junto a la plana docente, la revista indexada o la normativa
 * diría justo lo contrario. Estos tests fijan el límite, porque es la clase de
 * decisión que se erosiona sola con el tiempo.
 */
class MascotaInstitucionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** Las páginas donde la mascota sería ruido institucional. */
    public static function paginasSerias(): array
    {
        return [
            'plana docente' => ['/docentes'],
            'revista' => ['/revista'],
            'documentos' => ['/documentos'],
            'organigrama' => ['/organigrama'],
            'misión y visión' => ['/mision'],
        ];
    }

    #[DataProvider('paginasSerias')]
    public function test_the_mascot_stays_out_of_the_formal_pages(string $ruta): void
    {
        $html = $this->get($ruta)->assertOk()->getContent();

        foreach (['mascota-volando', 'mascota-leyendo', 'mascota-alumnas'] as $ilustracion) {
            $this->assertStringNotContainsString($ilustracion, $html, "La mascota se coló en {$ruta}.");
        }
    }

    public function test_the_reading_mascot_accompanies_an_empty_search(): void
    {
        $html = $this->get('/buscar?q='.urlencode('xyzzy sin resultados'))->assertOk()->getContent();

        $this->assertStringContainsString('Sin resultados', $html);
        $this->assertStringContainsString('mascota-leyendo', $html);
    }

    public function test_a_search_with_results_gets_no_mascot(): void
    {
        // Con resultados que leer, la ilustración solo estorbaría: el vacío era
        // la única razón para ponerla.
        $html = $this->get('/buscar?q=derecho')->assertOk()->getContent();

        $this->assertStringNotContainsString('mascota-leyendo', $html);
    }

    public function test_the_admissions_page_shows_the_students_illustration(): void
    {
        $html = $this->get('/perfil-ingreso')->assertOk()->getContent();

        $this->assertStringContainsString('mascota-alumnas', $html);
    }

    public function test_the_illustrations_are_decorative(): void
    {
        // Ninguna aporta información que no esté ya en el texto de al lado.
        // Anunciarlas a un lector de pantalla sería ruido, no accesibilidad.
        foreach ([
            '/buscar?q='.urlencode('xyzzy sin resultados') => 'mascota-leyendo',
            '/perfil-ingreso' => 'mascota-alumnas',
        ] as $ruta => $archivo) {
            $html = $this->get($ruta)->assertOk()->getContent();

            // La etiqueta concreta de esta ilustración, no la primera del
            // documento: la cabecera trae iconos con aria-hidden propios y
            // buscarlos sueltos daría por buena cualquier página.
            $etiqueta = '<img'.Str::before(Str::after($html, '<img src="'.asset("img/{$archivo}.webp").'"'), '>');

            $this->assertStringContainsString('alt=""', $etiqueta, "{$ruta}: la ilustración no lleva alt vacío.");
            $this->assertStringContainsString('aria-hidden="true"', $etiqueta, "{$ruta}: la ilustración no está oculta al lector de pantalla.");
        }
    }

    public function test_the_illustration_files_ship_with_the_application(): void
    {
        // Un enlace a una imagen que no existe deja un hueco roto en la página,
        // y nadie lo nota hasta que un visitante lo ve.
        foreach (['mascota-volando', 'mascota-leyendo', 'mascota-alumnas', 'mascota-derecho'] as $archivo) {
            $this->assertFileExists(public_path("img/{$archivo}.webp"));
        }
    }
}
