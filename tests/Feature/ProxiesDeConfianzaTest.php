<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Con Cloudflare delante, el portal tiene que ver la IP del visitante.
 *
 * Sin proxies declarados, «la IP del visitante» es la del borde de Cloudflare
 * para todo el mundo. Eso no rompe ninguna página, pero sí dos cosas que
 * dependen de distinguir a una persona de otra: el límite de cinco envíos por
 * hora, que pasaría a compartirse entre desconocidos, y la huella de IP que se
 * guarda con cada manuscrito, que dejaría de servir como rastro.
 */
class ProxiesDeConfianzaTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('TRUSTED_PROXIES=192.0.2.1');
        parent::setUp();
    }

    protected function tearDown(): void
    {
        putenv('TRUSTED_PROXIES');
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);
        parent::tearDown();
    }

    public function test_the_declared_proxy_is_trusted_at_boot(): void
    {
        $this->assertSame(['192.0.2.1'], config('seguridad.proxies_de_confianza'));
        $this->assertSame(['192.0.2.1'], Request::getTrustedProxies());
    }

    public function test_the_real_visitor_ip_is_read_from_behind_the_proxy(): void
    {
        $peticion = Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => '192.0.2.1',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.9',
        ]);

        $this->assertSame('203.0.113.9', $peticion->ip(),
            'El portal sigue viendo la IP del proxy en vez de la del visitante.');
    }

    public function test_an_undeclared_proxy_is_not_believed(): void
    {
        // Lo contrario también importa: cualquiera puede mandar una cabecera
        // X-Forwarded-For inventada. Solo se cree a los proxies declarados.
        $peticion = Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => '198.51.100.7',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.9',
        ]);

        $this->assertSame('198.51.100.7', $peticion->ip());
    }
}
