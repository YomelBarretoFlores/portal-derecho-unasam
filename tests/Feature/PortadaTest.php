<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Revista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * La portada es la página que decide la impresión que se lleva un visitante
 * académico. Estas comprobaciones fijan lo que debe mostrar y, sobre todo, lo
 * que no debe inventarse cuando falta información.
 */
class PortadaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function revista(array $extra = []): Revista
    {
        return Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>', 'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe', 'estado_editorial' => 'published',
            ...$extra,
        ]);
    }

    private function docente(string $nombre, int $orden): Docente
    {
        return Docente::query()->create([
            'name' => $nombre, 'area' => 'Derecho Civil', 'grado' => 'Doctor en Derecho',
            'orden' => $orden, 'activo' => true, 'estado_editorial' => 'published',
            // El modelo exige constancia documental antes de publicar un perfil.
            'documento_fuente' => 'Resolución de designación docente 2026',
            'estado_revision' => 'verificado',
            'slug' => Str::slug($nombre),
            'categoria' => 'Principal', 'dedicacion' => 'Tiempo completo',
            'resena' => 'Docente del Programa de Derecho y Ciencias Políticas.',
        ]);
    }

    public function test_the_credentials_band_publishes_the_journals_editorial_process(): void
    {
        $this->revista([
            'sistema_arbitraje' => 'Revisión por pares doble ciego',
            'norma_citacion' => 'APA 7.ª edición',
            'modalidad' => 'Digital',
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('Credenciales académicas', $html);
        $this->assertStringContainsString('Revisión por pares doble ciego', $html);
        $this->assertStringContainsString('APA 7.ª edición', $html);
    }

    public function test_the_credentials_band_never_invents_a_missing_issn(): void
    {
        // La revista es nueva y todavía no tiene ISSN. Rellenar ese hueco con
        // «en trámite» o con un guion sería afirmar algo que nadie ha verificado,
        // justo en la banda que existe para dar garantías.
        $this->revista([
            'sistema_arbitraje' => 'Revisión por pares doble ciego',
            'norma_citacion' => 'APA 7.ª edición',
            'modalidad' => 'Digital',
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('ISSN', $html);
    }

    public function test_the_credentials_band_is_omitted_when_there_is_almost_nothing_to_show(): void
    {
        // «periodicidad» es obligatoria para publicar, así que queda una sola
        // credencial: por debajo del mínimo, la banda no se dibuja.
        $this->revista();

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Credenciales académicas', $html);
    }

    public function test_the_homepage_shows_the_teaching_staff_with_a_real_count(): void
    {
        foreach (['Ana Ruiz', 'Beto Solís', 'Carla Vega'] as $i => $nombre) {
            $this->docente($nombre, $i + 1);
        }

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('Plana docente', $html);
        $this->assertStringContainsString('Ana Ruiz', $html);
        $this->assertStringContainsString('Ver los 3 docentes', $html);
    }

    public function test_the_teaching_staff_section_disappears_without_teachers(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Quiénes enseñan Derecho', $html);
    }

    public function test_the_homepage_leads_with_substance_before_news(): void
    {
        // PRODUCT.md sitúa como audiencia prioritaria a quien evalúa al programa:
        // esa lectura busca producción académica y datos, no novedades. El orden
        // de la portada tiene que reflejarlo.
        $this->revista();
        $this->docente('Ana Ruiz', 1);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $revista = strpos($html, 'home_revista') !== false ? null : strpos($html, 'Investigación jurídica original');
        $docentes = strpos($html, 'Quiénes enseñan Derecho');
        $blog = strpos($html, 'Noticias, opiniones y eventos');

        $this->assertNotFalse($docentes);
        $this->assertNotFalse($blog);
        $this->assertLessThan($blog, $docentes, 'La plana docente debe ir antes que la actualidad.');

        if ($revista !== false && $revista !== null) {
            $this->assertLessThan($docentes, $revista, 'La revista debe ir antes que la plana docente.');
        }
    }
}
