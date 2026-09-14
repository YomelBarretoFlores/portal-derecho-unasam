<?php

namespace Tests\Feature;

use App\Support\ProxiesDeConfianza;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Con Cloudflare delante, el portal tiene que ver la IP del visitante.
 *
 * Sin proxies declarados, «la IP del visitante» es la del borde de Cloudflare
 * para todo el mundo. Eso no rompe ninguna página —el esquema https se detecta
 * bien porque el origen se sirve por TLS—, pero sí rompe lo que depende de
 * distinguir a una persona de otra: el límite de cinco envíos por hora pasa a
 * compartirse entre desconocidos, y la huella de IP que se guarda con cada
 * manuscrito deja de servir como rastro.
 */
class ProxiesDeConfianzaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // «setTrustedProxies» es estado global del proceso: sin esto, la
        // configuración de un test se filtraría a los siguientes.
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);
        parent::tearDown();
    }

    private function conProxies(array $proxies): void
    {
        config()->set('seguridad.proxies_de_confianza', $proxies);
        ProxiesDeConfianza::aplicar();
    }

    private function peticionDetrasDe(string $remota, string $reenviada): Request
    {
        return Request::create('/', 'GET', server: [
            'REMOTE_ADDR' => $remota,
            'HTTP_X_FORWARDED_FOR' => $reenviada,
        ]);
    }

    public function test_the_real_visitor_ip_is_read_from_behind_a_declared_proxy(): void
    {
        $this->conProxies(['192.0.2.1']);

        $this->assertSame('203.0.113.9', $this->peticionDetrasDe('192.0.2.1', '203.0.113.9')->ip(),
            'El portal sigue viendo la IP del proxy en vez de la del visitante.');
    }

    public function test_a_cidr_range_works_because_cloudflare_publishes_ranges(): void
    {
        // Cloudflare no da IPs sueltas: publica 22 rangos. Si no se admitieran,
        // habría que listarlas una a una y quedaría siempre desactualizado.
        $this->conProxies(['162.158.0.0/15']);

        $this->assertSame('203.0.113.9', $this->peticionDetrasDe('162.158.44.7', '203.0.113.9')->ip());
    }

    public function test_an_undeclared_proxy_is_not_believed(): void
    {
        // Lo contrario importa igual: cualquiera puede mandar una cabecera
        // X-Forwarded-For inventada para saltarse los límites por IP.
        $this->conProxies(['192.0.2.1']);

        $this->assertSame('198.51.100.7', $this->peticionDetrasDe('198.51.100.7', '203.0.113.9')->ip());
    }

    public function test_a_wildcard_is_ignored_instead_of_trusting_everyone(): void
    {
        $this->conProxies(['*']);

        $this->assertSame([], Request::getTrustedProxies());
        $this->assertSame('198.51.100.7', $this->peticionDetrasDe('198.51.100.7', '203.0.113.9')->ip());
    }

    public function test_an_empty_list_means_there_is_no_proxy_and_nothing_breaks(): void
    {
        $this->conProxies([]);

        $this->get('/')->assertOk();
        $this->assertSame([], Request::getTrustedProxies());
    }
}
