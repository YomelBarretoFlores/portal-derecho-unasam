<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_current_published_blog_posts_are_public(): void
    {
        $published = BlogPost::create([
            'tipo' => 'noticia',
            'titulo' => 'Publicación vigente',
            'slug' => 'publicacion-vigente',
            'extracto' => 'Resumen institucional',
            'contenido' => '<script>alert(1)</script><p>Contenido seguro</p>',
            'fecha' => today(),
            'publicado' => true,
        ]);
        $draft = BlogPost::create([
            'tipo' => 'noticia', 'titulo' => 'Borrador', 'slug' => 'borrador',
            'contenido' => '<p>Borrador</p>', 'fecha' => today(), 'publicado' => false,
        ]);
        $future = BlogPost::create([
            'tipo' => 'evento', 'titulo' => 'Futuro', 'slug' => 'futuro',
            'contenido' => '<p>Futuro</p>', 'fecha' => today()->addDay(), 'publicado' => true,
        ]);

        $this->get(route('blog.show', $published))
            ->assertOk()
            ->assertSee('Contenido seguro')
            ->assertDontSee('alert(1)');
        $this->get(route('blog.show', $draft))->assertNotFound();
        $this->get(route('blog.show', $future))->assertNotFound();
    }

    public function test_journal_routes_enforce_issue_and_article_publication_and_scope(): void
    {
        $journal = Revista::create([
            'nombre' => 'Derecho y Cultura',
            'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación</p>',
            'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => 'N.° 063-2026',
            'resolucion_fecha' => today(),
            'resolucion_resumen' => 'Creación de la revista.',
            'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@example.edu.pe',
            'activo' => true,
            'publicado' => true,
        ]);
        $issue = RevistaNumero::create([
            'revista_id' => $journal->id, 'volumen' => '1', 'numero' => '1',
            'slug' => 'vol-1-num-1', 'titulo' => 'Primer número',
            'fecha_publicacion' => today(), 'publicado' => true,
        ]);
        $otherIssue = RevistaNumero::create([
            'revista_id' => $journal->id, 'volumen' => '1', 'numero' => '2',
            'slug' => 'vol-1-num-2', 'titulo' => 'Segundo número',
            'fecha_publicacion' => today(), 'publicado' => true,
        ]);
        $article = Articulo::create([
            'revista_numero_id' => $issue->id, 'titulo' => 'Artículo verificado',
            'slug' => 'articulo-verificado', 'autores' => ['Autora Verificada'],
            'categoria' => 'Derecho', 'resumen' => 'Resumen', 'contenido' => '<p>Texto académico</p>',
            'fecha' => today(), 'publicado' => true,
        ]);

        $this->get(route('revista.numero', $issue))->assertOk()->assertSee($article->titulo);
        $this->get(route('revista.articulo', [$issue, $article]))->assertOk()->assertSee('Texto académico');
        $this->get(route('revista.articulo', [$otherIssue, $article]))->assertNotFound();
    }

    public function test_draft_comunicado_is_not_public_or_in_sitemap(): void
    {
        $draft = Comunicado::create([
            'titulo' => 'Comunicado interno', 'slug' => 'comunicado-interno',
            'contenido' => '<p>Interno</p>', 'fecha_publicacion' => now(), 'publicado' => false,
        ]);
        $published = Comunicado::create([
            'titulo' => 'Comunicado público', 'slug' => 'comunicado-publico',
            'contenido' => '<p>Público</p>', 'fecha_publicacion' => now(), 'publicado' => true,
        ]);

        $this->get(route('comunicados.show', $draft))->assertNotFound();
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('comunicados.show', $published), false)
            ->assertDontSee(route('comunicados.show', $draft), false);
    }
}
