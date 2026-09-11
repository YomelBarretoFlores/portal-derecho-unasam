<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Comunicado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * El portal no tenía buscador. En un sitio institucional donde la mayor parte
 * del contenido son páginas estáticas —presentación, planes, perfiles—, buscar
 * suele ser más rápido que recorrer el menú.
 */
class BuscadorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function comunicado(string $titulo, bool $publicado = true): Comunicado
    {
        return Comunicado::query()->create([
            'titulo' => $titulo, 'resumen' => 'Resumen del comunicado.', 'contenido' => '<p>Cuerpo.</p>',
            'publicado' => $publicado, 'fecha_publicacion' => '2026-03-01',
            'estado_editorial' => $publicado ? 'published' : 'draft',
        ]);
    }

    public function test_it_finds_static_pages_ignoring_case_and_accents(): void
    {
        // «mision» sin tilde y en minúsculas debe encontrar «Misión y Visión».
        // Resolverlo en SQL exigiría la extensión unaccent de PostgreSQL, que no
        // está garantizada en el alojamiento y no existe en SQLite.
        $this->get(route('buscar', ['q' => 'MISION']))
            ->assertOk()
            ->assertSee('Misión y Visión');
    }

    public function test_it_finds_published_content(): void
    {
        $this->comunicado('Convocatoria de prácticas preprofesionales');

        $this->get(route('buscar', ['q' => 'practicas']))
            ->assertOk()
            ->assertSee('Convocatoria de prácticas preprofesionales');
    }

    public function test_it_never_reveals_unpublished_content(): void
    {
        $this->comunicado('Borrador que no debe salir', publicado: false);
        BlogPost::query()->create([
            'tipo' => 'noticia', 'titulo' => 'Entrada aún sin publicar', 'slug' => 'sin-publicar',
            'extracto' => 'E.', 'contenido' => '<p>C.</p>', 'autor' => 'Redacción',
            'fecha' => '2026-02-20', 'publicado' => false, 'estado_editorial' => 'draft',
        ]);

        $this->get(route('buscar', ['q' => 'borrador']))->assertOk()->assertDontSee('Borrador que no debe salir');
        $this->get(route('buscar', ['q' => 'publicar']))->assertOk()->assertDontSee('Entrada aún sin publicar');
    }

    public function test_every_word_must_match(): void
    {
        $this->comunicado('Convocatoria de prácticas preprofesionales');

        // «practicas» sí está; «arquitectura» no: el resultado no debe aparecer.
        $this->get(route('buscar', ['q' => 'practicas arquitectura']))
            ->assertOk()
            ->assertDontSee('Convocatoria de prácticas preprofesionales')
            ->assertSee('Sin resultados');
    }

    public function test_an_empty_query_does_not_error_or_pretend_to_search(): void
    {
        $this->get(route('buscar'))
            ->assertOk()
            ->assertDontSee('Sin resultados')
            ->assertSee('Escribe lo que busques');
    }

    public function test_a_one_letter_query_returns_nothing_instead_of_everything(): void
    {
        $this->get(route('buscar', ['q' => 'a']))->assertOk()->assertSee('Sin resultados');
    }

    public function test_the_results_page_is_not_indexed(): void
    {
        // Las páginas de resultados no deben acabar en buscadores externos:
        // multiplican direcciones sin contenido propio.
        $this->get(route('buscar', ['q' => 'mision']))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false);
    }

    public function test_the_search_is_reachable_from_every_page(): void
    {
        $this->get(route('home'))->assertOk()->assertSee(route('buscar', absolute: false), false);
    }
}
