<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * El 404 es la única página del portal donde la mascota lleva la voz cantante:
 * no compite con contenido oficial porque no hay ninguno que mostrar. Eso la
 * obliga a dos cosas contrarias —aparecer cuando la facultad la ha subido y
 * desaparecer del todo cuando no—, así que conviene fijar las dos.
 */
class PaginaNoEncontradaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_the_mascot_accompanies_the_not_found_page(): void
    {
        config()->set('seguridad.csp.img_hosts', ['cdn.unasam.edu.pe']);
        Setting::set('home_hero_mascota_url', 'https://cdn.unasam.edu.pe/mascota.webp');

        $this->get('/esta-ruta-no-existe')
            ->assertNotFound()
            ->assertSee('https://cdn.unasam.edu.pe/mascota.webp')
            ->assertSee('mascota-404');
    }

    public function test_the_not_found_page_stands_on_its_own_without_a_mascot(): void
    {
        // Vaciar el campo en el panel tiene que quitarla de verdad, y la página
        // sin ella no puede quedar coja: el enlace de vuelta es lo único que
        // esta página existe para ofrecer.
        Setting::set('home_hero_mascota_url', '');

        $html = $this->get('/esta-ruta-no-existe')->assertNotFound()->getContent();

        $this->assertStringNotContainsString('mascota-404', $html);
        $this->assertStringContainsString('Volver al inicio', $html);
    }

    public function test_the_mascot_is_decorative_and_not_announced(): void
    {
        // No aporta nada a quien no la ve: el mensaje entero ya está en el
        // texto de al lado. Anunciarla sería ruido, no accesibilidad.
        config()->set('seguridad.csp.img_hosts', ['cdn.unasam.edu.pe']);
        Setting::set('home_hero_mascota_url', 'https://cdn.unasam.edu.pe/mascota.webp');

        $html = $this->get('/esta-ruta-no-existe')->assertNotFound()->getContent();

        $bloque = Str::before(Str::after($html, 'mascota-404'), '</div>');

        $this->assertStringContainsString('aria-hidden="true"', Str::before($html, 'mascota.webp'));
        $this->assertStringContainsString('alt=""', $bloque);
    }
}
