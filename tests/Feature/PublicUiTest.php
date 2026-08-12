<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\Acceso;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PublicUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_editorial_empty_states_without_hiding_content_behind_javascript(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Actualidad en preparación')
            ->assertSee('editorial-empty', false)
            ->assertSee('Navegación principal sin JavaScript')
            ->assertDontSee("document.documentElement.classList.add('js')", false);

        $css = File::get(resource_path('css/app.css'));
        $this->assertStringNotContainsString('.js .reveal', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
    }

    public function test_homepage_gives_a_single_post_a_full_editorial_layout(): void
    {
        $this->createPublishedPost('Noticia institucional', 'noticia-institucional');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Noticia institucional')
            ->assertSee('Actualidad institucional')
            ->assertSee('md:grid-cols-[minmax(15rem,0.72fr)_minmax(0,1.28fr)]', false);
    }

    public function test_homepage_uses_an_asymmetric_layout_for_two_posts(): void
    {
        $this->createPublishedPost('Primera noticia', 'primera-noticia');
        $this->createPublishedPost('Segunda noticia', 'segunda-noticia');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Primera noticia')
            ->assertSee('Segunda noticia')
            ->assertSee('lg:grid-cols-[1.2fr_0.8fr]', false);
    }

    public function test_homepage_access_grid_adapts_to_real_record_count(): void
    {
        Acceso::query()->create([
            'titulo' => 'Plan de Estudios',
            'descripcion' => 'Consulta la malla curricular.',
            'url' => '/plan-2023',
            'activo' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Recursos destacados')
            ->assertSee('Plan de Estudios')
            ->assertDontSee('lg:grid-cols-4', false);
    }

    private function createPublishedPost(string $titulo, string $slug): BlogPost
    {
        return BlogPost::query()->create([
            'tipo' => 'noticia',
            'titulo' => $titulo,
            'slug' => $slug,
            'extracto' => 'Información institucional verificada para la comunidad universitaria.',
            'contenido' => '<p>Contenido institucional publicado.</p>',
            'autor' => 'Facultad de Derecho y Ciencias Políticas',
            'tiempo_lectura' => '2 min',
            'fecha' => now()->toDateString(),
            'estado_editorial' => EditorialStatus::Published->value,
        ]);
    }
}
