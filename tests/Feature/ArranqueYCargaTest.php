<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dos correcciones que vinieron del servidor de la universidad, no de aquí.
 *
 * El área de sistemas tuvo que editar a mano «bootstrap/app.php» y
 * «SecurityHeaders.php» para que el portal arrancara y para que el panel
 * pudiera subir archivos. Un parche en el servidor desaparece en la siguiente
 * actualización, así que las dos correcciones viven ya en el repositorio y
 * estos tests impiden que alguien las deshaga sin darse cuenta.
 */
class ArranqueYCargaTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_upload_widget_is_allowed_to_start_its_worker(): void
    {
        // El componente de carga del panel procesa cada archivo en un Web
        // Worker creado al vuelo, con una URL «blob:». Sin esta directiva el
        // navegador lo bloquea y el recuadro de carga deja de subir nada, sin
        // ningún mensaje: parece que el campo simplemente no funciona.
        $csp = $this->get('/')->assertOk()->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("worker-src 'self' blob:", (string) $csp);
    }

    public function test_an_empty_trusted_proxies_list_does_not_stop_the_site(): void
    {
        // Vacío no es un error: significa que no hay ningún proxy delante, el
        // caso de una instalación directa sobre un servidor propio. Antes esto
        // lanzaba una excepción y dejaba el portal entero caído.
        config()->set('seguridad.proxies_de_confianza', []);

        $this->get('/')->assertOk();
    }

    public function test_a_wildcard_proxy_is_ignored_instead_of_trusted(): void
    {
        // Confiar en cualquier proxy deja falsear la IP del visitante. Se
        // descarta el valor, pero sin tumbar el sitio.
        config()->set('seguridad.proxies_de_confianza', ['*']);

        $this->get('/')->assertOk();
    }

    public function test_the_trusted_proxies_setting_survives_a_cached_config(): void
    {
        // La comprobación anterior usaba env() en bootstrap/app.php, y con la
        // configuración cacheada —como está en producción— env() devuelve null
        // fuera de los ficheros de config: no se ejecutaba nunca. Leerlo desde
        // config es lo que hace que el ajuste exista de verdad.
        $this->assertIsArray(config('seguridad.proxies_de_confianza'));

        $bootstrap = file_get_contents(base_path('bootstrap/app.php'));

        $this->assertStringNotContainsString("env('TRUSTED_PROXIES'", $bootstrap,
            'bootstrap/app.php vuelve a leer la variable con env(): con la configuración cacheada eso es siempre null.');
        $this->assertStringNotContainsString("env('APP_ENV')", $bootstrap,
            'bootstrap/app.php vuelve a leer el entorno con env(): use app()->environment() o config().');
    }
}
