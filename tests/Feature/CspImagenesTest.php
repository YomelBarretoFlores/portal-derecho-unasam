<?php

namespace Tests\Feature;

use App\Rules\ImagenVisible;
use App\Support\OrigenesDeImagen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

/**
 * La cabecera Content-Security-Policy y los dominios de las imágenes.
 *
 * Es un fallo especialmente desagradable porque no se parece a un fallo: el
 * archivo se guarda, el formulario dice que todo fue bien, y la página sale
 * con un hueco. Nada en la interfaz señala al navegador, que es quien decidió
 * no pintarla.
 */
class CspImagenesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // El servidor de desarrollo de Vite añade su propio origen a img-src.
        // Si está corriendo en la máquina de quien ejecuta los tests, estas
        // comprobaciones darían resultados distintos según el momento.
        Vite::useHotFile(storage_path('framework/testing/sin-vite-hot'));
    }

    protected function tearDown(): void
    {
        Vite::useHotFile(public_path('hot'));

        parent::tearDown();
    }

    private function csp(): string
    {
        return (string) $this->get('/')->headers->get('Content-Security-Policy');
    }

    private function imgSrc(): string
    {
        foreach (explode(';', $this->csp()) as $directiva) {
            if (str_starts_with(trim($directiva), 'img-src')) {
                return trim($directiva);
            }
        }

        return '';
    }

    public function test_by_default_only_the_portal_itself_can_serve_images(): void
    {
        $this->assertSame("img-src 'self' data: blob:", $this->imgSrc());
    }

    public function test_the_media_bucket_is_allowed_without_naming_it_twice(): void
    {
        // Quien configura el proveedor rellena AWS_URL y nada más. Si además
        // hubiera que acordarse de repetir el dominio en otra variable, el
        // despliegue funcionaría a medias: las imágenes se subirían bien y no
        // se verían, que es justo el fallo difícil de diagnosticar.
        config()->set('media-library.disk_name', 'medios');
        config()->set('filesystems.disks.medios.url', 'https://medios.derecho.unasam.edu.pe/portal');

        $this->assertStringContainsString('https://medios.derecho.unasam.edu.pe', $this->imgSrc());

        // Solo el origen: la ruta del bucket no pinta nada en una directiva CSP.
        $this->assertStringNotContainsString('/portal', $this->imgSrc());
    }

    public function test_declared_hosts_are_allowed_and_the_policy_is_never_opened_up(): void
    {
        config()->set('seguridad.csp.img_hosts', ['repositorio.unasam.edu.pe', '*', 'http://inseguro.example']);

        $imgSrc = $this->imgSrc();

        $this->assertStringContainsString('https://repositorio.unasam.edu.pe', $imgSrc);

        // Un comodín anularía la política entera, y un origen sin cifrar lo
        // bloquearía el navegador igualmente en una página servida por https.
        $this->assertStringNotContainsString('*', $imgSrc);
        $this->assertStringNotContainsString('inseguro.example', $imgSrc);
    }

    public function test_the_portals_own_origin_is_not_repeated(): void
    {
        config()->set('seguridad.csp.img_hosts', [config('app.url')]);

        // Ya lo cubre 'self'; repetirlo solo alarga la cabecera.
        $this->assertSame("img-src 'self' data: blob:", $this->imgSrc());
    }

    public function test_the_admin_refuses_an_image_the_browser_would_block(): void
    {
        config()->set('seguridad.csp.img_hosts', []);

        $validador = Validator::make(
            ['portada_url_respaldo' => 'https://imgur.example/portada.jpg'],
            ['portada_url_respaldo' => [new ImagenVisible]],
        );

        $this->assertTrue($validador->fails());
        $this->assertStringContainsString(
            'CSP_IMG_HOSTS',
            (string) $validador->errors()->first('portada_url_respaldo'),
        );
    }

    public function test_the_admin_accepts_an_image_from_a_declared_host(): void
    {
        config()->set('seguridad.csp.img_hosts', ['repositorio.unasam.edu.pe']);

        $validador = Validator::make(
            ['portada_url_respaldo' => 'https://repositorio.unasam.edu.pe/portadas/1.jpg'],
            ['portada_url_respaldo' => [new ImagenVisible]],
        );

        $this->assertFalse($validador->fails());
    }

    public function test_internal_paths_and_empty_values_are_always_accepted(): void
    {
        foreach (['', '/img/revista/portada.jpg', 'data:image/svg+xml;base64,AAAA'] as $valor) {
            $this->assertTrue(OrigenesDeImagen::admite($valor), "Debería admitir «{$valor}».");
        }

        // Una URL sin esquema ni barra inicial no es una ruta interna.
        $this->assertFalse(OrigenesDeImagen::admite('//evil.example/x.jpg'));
    }
}
