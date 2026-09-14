<?php

namespace Tests\Feature;

use App\Models\Revista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Las direcciones que acaban en la caché tienen que ser relativas.
 *
 * El logo y la resolución de la revista se resolvían con asset(), que compone
 * la dirección con el dominio de quien provocó el guardado de la caché. Y esas
 * dos direcciones se guardan en la caché de contenido público, así que basta
 * con calentarla desde una consola —o desde otro puerto— para que TODOS los
 * visitantes reciban una dirección que no existe. Se vio en desarrollo: el
 * logo apuntaba a «http://localhost:8000» y salía el icono de imagen rota.
 *
 * Es el mismo fallo que tenían los archivos subidos, por otro camino.
 */
class RutasDeArchivosDelRepositorioTest extends TestCase
{
    use RefreshDatabase;

    private function revista(): Revista
    {
        return Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'estado_editorial' => 'draft',
        ]);
    }

    public function test_the_bundled_logo_is_served_from_whatever_host_asked_for_it(): void
    {
        config()->set('app.url', 'http://localhost:8000');

        $url = $this->revista()->logo_url;

        $this->assertStringStartsWith('/', $url, "El logo trae dominio: {$url}");
        $this->assertStringNotContainsString('localhost', $url);
        $this->assertStringContainsString(Revista::LOGO_PUBLIC_PATH, $url);
    }

    public function test_the_bundled_resolution_pdf_behaves_the_same(): void
    {
        config()->set('app.url', 'https://otro-dominio.example');

        $url = $this->revista()->resolution_url;

        $this->assertStringStartsWith('/', $url, "La resolución trae dominio: {$url}");
        $this->assertStringNotContainsString('otro-dominio', $url);
    }

    public function test_a_warmed_cache_does_not_freeze_one_visitors_host(): void
    {
        // Este es el fallo de verdad: no que la dirección sea fea, sino que la
        // primera visita se la deje escrita a todas las demás.
        Cache::flush();
        $this->revista();
        config()->set('app.url', 'http://localhost:8000');

        $html = $this->get('/revista')->assertOk()->getContent();

        $this->assertStringNotContainsString('localhost:8000/img/revista', $html);
    }
}
