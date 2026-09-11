<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Comunicado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * El hero de la portada muestra una tira con lo último publicado. Es la única
 * parte del sitio que mezcla tres fuentes —comunicados, blog y avisos de la
 * revista—, así que conviene fijar su comportamiento: qué aparece, en qué orden
 * y qué pasa cuando no hay nada que mostrar.
 */
class HeroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function comunicado(string $titulo, string $fecha): Comunicado
    {
        return Comunicado::query()->create([
            'titulo' => $titulo, 'resumen' => 'R.', 'contenido' => '<p>C.</p>',
            'publicado' => true, 'fecha_publicacion' => $fecha, 'estado_editorial' => 'published',
        ]);
    }

    public function test_the_hero_strip_lists_the_latest_published_items(): void
    {
        $this->comunicado('Convocatoria de prácticas', '2026-01-10');
        BlogPost::query()->create([
            'tipo' => 'noticia', 'titulo' => 'Nueva biblioteca jurídica', 'slug' => 'nueva-biblioteca',
            'extracto' => 'E.', 'contenido' => '<p>C.</p>', 'autor' => 'Redacción',
            'fecha' => '2026-02-20', 'publicado' => true, 'estado_editorial' => 'published',
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('Lo último', $html);
        $this->assertStringContainsString('Nueva biblioteca jurídica', $html);
        $this->assertStringContainsString('Convocatoria de prácticas', $html);

        // Lo más reciente primero: la entrada de febrero antes del comunicado de enero.
        $this->assertLessThan(
            strpos($html, 'Convocatoria de prácticas'),
            strpos($html, 'Nueva biblioteca jurídica'),
            'La tira no está ordenada por fecha descendente.',
        );
    }

    public function test_the_hero_strip_disappears_when_there_is_nothing_published(): void
    {
        // El sitio arrancó sin contenido y volverá a estar así en una instalación
        // nueva: una tira vacía con flechas que no llevan a ninguna parte sería
        // peor que no tener tira.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Lo último', $html);
    }

    public function test_the_strip_has_no_controls_with_a_single_item(): void
    {
        $this->comunicado('Único aviso', '2026-03-01');

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('Único aviso', $html);
        $this->assertStringNotContainsString('aria-label="Siguiente"', $html);
    }

    public function test_the_hero_photograph_is_described(): void
    {
        // Es la única fotografía institucional del sitio y ocupa el hero entero:
        // sin alt, quien usa lector de pantalla no recibe nada de la portada.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('campus-derecho', $html);
        $this->assertMatchesRegularExpression('/alt="Patio de la Facultad[^"]+Cordillera Blanca[^"]*"/u', $html);
    }
}
