<?php

namespace Tests\Feature;

use App\Models\Revista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La portada de la revista pinta el logo en un cuadro de 80x80.
 *
 * El archivo que traía el repositorio mide 1254x1254 y pesa 159 KB, así que
 * cada visita se descargaba ciento cincuenta kilobytes para no usarlos. No era
 * un fallo visible —el logo se veía bien—, y por eso llevaba ahí desde el
 * principio.
 */
class LogoDeLaRevistaTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_small_logo_that_ships_with_the_app_is_actually_small(): void
    {
        $grande = public_path(Revista::LOGO_PUBLIC_PATH);
        $pequeno = public_path(Revista::LOGO_MINIATURA_PUBLIC_PATH);

        $this->assertFileExists($pequeno);

        [$ancho, $alto] = getimagesize($pequeno);
        $this->assertLessThanOrEqual(320, $ancho, 'La versión reducida no es reducida.');
        $this->assertSame($ancho, $alto, 'El logo es cuadrado y se recorta cuadrado.');
        $this->assertLessThan(
            filesize($grande) / 4,
            filesize($pequeno),
            'La versión reducida no ahorra lo suficiente como para justificar tener dos archivos.',
        );
    }

    public function test_it_falls_back_to_the_small_file_when_nothing_was_uploaded(): void
    {
        $revista = Revista::query()->create([
            'nombre' => 'Revista X', 'nombre_corto' => 'RX', 'estado_editorial' => 'draft',
        ]);

        $this->assertSame('/'.Revista::LOGO_MINIATURA_PUBLIC_PATH, $revista->logo_miniatura_url);

        // El grande sigue disponible: es el que se manda como og:image.
        $this->assertSame('/'.Revista::LOGO_PUBLIC_PATH, $revista->logo_url);
    }

    public function test_an_uploaded_logo_gets_a_thumbnail_and_the_small_url_uses_it(): void
    {
        config()->set('media.uploads_enabled', true);

        $revista = Revista::query()->create([
            'nombre' => 'Revista X', 'nombre_corto' => 'RX', 'estado_editorial' => 'draft',
        ]);

        $revista->addMedia(public_path('img/revista/logo-derecho-y-cultura.jpg'))
            ->preservingOriginal()
            ->toMediaCollection('logo');
        $revista->refresh();

        $media = $revista->getFirstMedia('logo');
        $this->assertTrue($media->hasGeneratedConversion('thumb'), 'No se generó la miniatura del logo subido.');

        [$ancho] = getimagesize($media->getPath('thumb'));
        $this->assertLessThanOrEqual(160, $ancho);

        $this->assertStringContainsString('thumb', $revista->logo_miniatura_url);
        $this->assertStringNotContainsString('thumb', $revista->logo_url);

        $revista->clearMediaCollection('logo');
    }

    public function test_an_external_logo_url_is_used_as_is(): void
    {
        // De una dirección externa no podemos generar nada; devolverla tal cual
        // es mejor que enseñar el logo del repositorio, que sería otro logo.
        $revista = Revista::query()->create([
            'nombre' => 'Revista X', 'nombre_corto' => 'RX', 'estado_editorial' => 'draft',
            'logo_url_respaldo' => 'https://ejemplo.test/logo.png',
        ]);

        $this->assertSame('https://ejemplo.test/logo.png', $revista->logo_miniatura_url);
    }
}
