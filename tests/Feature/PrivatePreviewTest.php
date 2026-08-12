<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Revista;
use App\Models\RevistaNumero;
use App\Models\User;
use App\Support\PreviewUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivatePreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_preview_requires_authentication_and_a_valid_signature(): void
    {
        $post = BlogPost::query()->create([
            'tipo' => 'noticia', 'titulo' => 'Borrador institucional', 'slug' => 'borrador-institucional',
            'contenido' => '<p>Contenido</p>', 'publicado' => false,
        ]);
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $signedUrl = PreviewUrl::for($post);

        $this->get($signedUrl)->assertRedirect('/admin/login');
        $this->actingAs($user)->get(route('preview.blog', $post))->assertForbidden();
        $this->actingAs($user)->get($signedUrl)
            ->assertOk()
            ->assertSee('Vista previa privada')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->get(route('blog.show', $post))->assertNotFound();
    }

    public function test_all_editorial_types_have_an_authorized_private_preview(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $revista = Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura', 'publicado' => false, 'activo' => false,
        ]);
        $numero = RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1', 'slug' => 'vol-1-num-1',
            'titulo' => 'Primer número', 'publicado' => false,
        ]);

        $records = [
            BlogPost::query()->create(['tipo' => 'noticia', 'titulo' => 'Blog', 'slug' => 'blog', 'contenido' => 'Texto', 'publicado' => false]),
            Comunicado::query()->create(['titulo' => 'Comunicado', 'slug' => 'comunicado', 'contenido' => 'Texto', 'publicado' => false]),
            Docente::query()->create(['name' => 'Docente Oficial', 'slug' => 'docente-oficial', 'resena' => 'Reseña', 'estado_revision' => 'pending', 'activo' => false]),
            $revista,
            $numero,
            Articulo::query()->create([
                'revista_numero_id' => $numero->id, 'titulo' => 'Artículo', 'slug' => 'articulo',
                'autores' => ['Autora'], 'categoria' => 'Derecho', 'resumen' => 'Resumen', 'contenido' => 'Texto', 'publicado' => false,
            ]),
        ];

        $this->actingAs($user);
        foreach ($records as $record) {
            $this->get(PreviewUrl::for($record))
                ->assertOk()
                ->assertSee('Vista previa privada');
        }
    }
}
