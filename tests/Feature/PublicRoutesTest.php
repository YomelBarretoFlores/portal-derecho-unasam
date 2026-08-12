<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_routes_render_with_empty_content(): void
    {
        $routes = [
            '/', '/presentacion', '/resumen', '/historia', '/mision', '/campo-laboral',
            '/objetivos', '/plan-2023', '/plan-2019', '/competencias', '/perfil-ingreso',
            '/perfil-egreso', '/revista', '/blog', '/docentes', '/comunicados',
            '/estadisticas/matriculados', '/organigrama', '/documentos', '/sitemap.xml',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertOk();
        }
    }

    public function test_security_headers_are_present(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_local_csp_allows_only_the_loopback_vite_development_server(): void
    {
        $hotFile = storage_path('framework/testing/vite-hot');
        File::ensureDirectoryExists(dirname($hotFile));
        File::put($hotFile, 'http://127.0.0.1:5173');
        Vite::useHotFile($hotFile);

        try {
            $csp = $this->get('/')->headers->get('Content-Security-Policy');

            $this->assertStringContainsString('style-src', $csp);
            $this->assertStringContainsString('http://127.0.0.1:5173', $csp);
            $this->assertStringContainsString('ws://127.0.0.1:5173', $csp);
        } finally {
            File::delete($hotFile);
            Vite::useHotFile(public_path('hot'));
        }
    }
}
