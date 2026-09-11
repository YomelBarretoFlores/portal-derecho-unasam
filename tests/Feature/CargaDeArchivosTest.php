<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Adjuntar un archivo tiene que funcionar de verdad, contra disco real.
 *
 * No vale simular el almacenamiento: lo que falló en el servidor de la
 * universidad fue precisamente el paso que un «fake» se salta —mover el
 * archivo a su sitio y generar la miniatura—. Un test con Storage::fake
 * habría pasado en verde mientras el portal real devolvía 500 en cada carga.
 *
 * Sirve además de control: si esto pasa aquí y falla allí, el problema es del
 * servidor y no del código, que es justo la pregunta que había que responder.
 */
class CargaDeArchivosTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_image_with_thumbnail_is_attached_and_converted(): void
    {
        $post = BlogPost::query()->create([
            'tipo' => 'noticia', 'titulo' => 'Con imagen', 'slug' => 'con-imagen',
            'extracto' => 'E.', 'contenido' => '<p>C.</p>', 'autor' => 'R.',
            'fecha' => '2026-01-01', 'publicado' => false, 'estado_editorial' => 'draft',
        ]);

        $post->addMedia(public_path('img/mascota-volando.webp'))
            ->preservingOriginal()
            ->toMediaCollection('imagen');

        $medio = $post->fresh()->getFirstMedia('imagen');

        $this->assertNotNull($medio, 'No se adjuntó la imagen.');
        $this->assertFileExists($medio->getPath(), 'El original no llegó al disco.');
        $this->assertFileExists($medio->getPath('thumb'), 'No se generó la miniatura (¿falta GD?).');
    }

    public function test_a_pdf_is_attached_without_any_conversion(): void
    {
        $revista = Revista::query()->create([
            'nombre' => 'Revista X', 'nombre_corto' => 'RX', 'estado_editorial' => 'draft',
        ]);

        $numero = RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1',
            'titulo' => 'Número uno', 'slug' => 'numero-uno', 'estado_editorial' => 'draft',
        ]);

        $numero->addMedia(public_path('docs/revista/normas-publicacion-v1.pdf'))
            ->preservingOriginal()
            ->toMediaCollection('numero_pdf');

        $medio = $numero->fresh()->getFirstMedia('numero_pdf');

        $this->assertNotNull($medio, 'No se adjuntó el PDF.');
        $this->assertFileExists($medio->getPath(), 'El PDF no llegó al disco.');
    }

    public function test_the_image_library_is_actually_available(): void
    {
        // Si esto falla, ninguna carga de imagen puede funcionar por mucho que
        // los permisos del disco estén bien.
        $this->assertTrue(
            extension_loaded('gd') || extension_loaded('imagick'),
            'PHP no tiene ni GD ni Imagick: el portal no puede generar miniaturas.',
        );
    }
}
