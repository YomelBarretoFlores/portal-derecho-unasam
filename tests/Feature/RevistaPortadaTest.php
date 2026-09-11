<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * La portada de la revista se comporta como la de cualquier publicación seria:
 * el número en curso encabeza, con su tabla de contenidos. Antes la colección
 * vivía al final de la página, detrás de la presentación y la ficha.
 */
class RevistaPortadaTest extends TestCase
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

    private function numero(Revista $revista, string $titulo, string $fecha, bool $actual = false): RevistaNumero
    {
        return RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => $actual ? '2' : '1',
            'slug' => Str::slug($titulo), 'titulo' => $titulo, 'fecha_publicacion' => $fecha,
            'es_actual' => $actual, 'estado_editorial' => 'published',
        ]);
    }

    private function articulo(RevistaNumero $numero, string $titulo, int $orden): Articulo
    {
        return Articulo::query()->create([
            'revista_numero_id' => $numero->id, 'titulo' => $titulo, 'slug' => Str::slug($titulo),
            'autores' => ['Ana Ruiz'], 'categoria' => 'Ensayo', 'resumen' => 'R.',
            'contenido' => '<p>C.</p>', 'fecha' => '2026-06-01', 'orden' => $orden,
            'estado_editorial' => 'published',
        ]);
    }

    public function test_the_current_issue_leads_the_page_with_its_table_of_contents(): void
    {
        $revista = $this->revista();
        $numero = $this->numero($revista, 'Justicia intercultural', '2026-06-01', actual: true);
        $this->articulo($numero, 'Segundo artículo', 2);
        $this->articulo($numero, 'Primer artículo', 1);

        $html = $this->get(route('revista'))->assertOk()->getContent();

        $this->assertStringContainsString('Número actual', $html);
        $this->assertStringContainsString('Tabla de contenidos', $html);

        // Encabeza: aparece antes del archivo editorial.
        $this->assertLessThan(
            strpos($html, 'Números publicados'),
            strpos($html, 'Número actual'),
            'El número en curso debe encabezar la portada, no ir detrás del archivo.',
        );

        // La tabla de contenidos respeta el campo «orden», no el alfabético.
        $this->assertLessThan(
            strpos($html, 'Segundo artículo'),
            strpos($html, 'Primer artículo'),
            'La tabla de contenidos no respeta el orden editorial.',
        );
    }

    public function test_the_marked_current_issue_wins_over_the_most_recent_one(): void
    {
        $revista = $this->revista();
        $this->numero($revista, 'Número más reciente', '2026-09-01');
        $this->numero($revista, 'Número marcado como actual', '2026-01-01', actual: true);

        $html = $this->get(route('revista'))->assertOk()->getContent();

        $posicionActual = strpos($html, 'Número actual');
        $marcado = strpos($html, 'Número marcado como actual');

        $this->assertNotFalse($marcado);
        $this->assertGreaterThan($posicionActual, $marcado);
        $this->assertLessThan(strpos($html, 'Números publicados'), $marcado);
    }

    public function test_the_page_does_not_pretend_to_have_a_current_issue(): void
    {
        $this->revista();

        $html = $this->get(route('revista'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Número actual', $html);
        $this->assertStringContainsString('Primera edición', $html);
    }

    public function test_the_peer_review_system_comes_from_the_panel_and_is_not_hardcoded(): void
    {
        // La ficha afirmaba «Doble ciego» escrito en la plantilla, aunque el dato
        // es administrable: si el equipo editorial cambiaba el sistema de
        // arbitraje, la página pública seguía anunciando el anterior.
        $this->revista([
            'sistema_arbitraje' => 'Revisión abierta por pares',
            'norma_citacion' => 'APA 7.ª edición',
            'modalidad' => 'Digital',
        ]);

        $html = $this->get(route('revista'))->assertOk()->getContent();

        $this->assertStringContainsString('Revisión abierta por pares', $html);
        $this->assertStringContainsString('APA 7.ª edición', $html);
        $this->assertStringNotContainsString('>Doble ciego<', $html);
    }
}
