<?php

namespace Tests\Feature;

use Livewire\Mechanisms\HandleRequests\EndpointResolver;
use Tests\TestCase;

/**
 * La dirección por la que habla el panel depende de la APP_KEY.
 *
 * Importa porque el servidor de la UNASAM tiene un WAF delante que da falsos
 * positivos sobre esa ruta, y la exclusión que lo corrige se escribe una vez en
 * la configuración de ModSecurity. Si alguien regenera la APP_KEY, la ruta
 * cambia, la exclusión deja de aplicarse y el panel vuelve a fallar a ratos sin
 * que nada apunte al WAF.
 *
 * Esta prueba no impide ese cambio —no puede—, pero deja escrito el vínculo y
 * comprueba que la ruta sigue teniendo la forma que espera la exclusión.
 * Véase DEPLOY.md, «Si hay un WAF delante».
 */
class RutaDeLivewireTest extends TestCase
{
    public function test_the_panel_endpoint_matches_the_shape_the_waf_rule_expects(): void
    {
        // El patrón de la exclusión documentada: ^/livewire-[0-9a-f]{8}/
        $this->assertMatchesRegularExpression('#^/livewire-[0-9a-f]{8}$#', EndpointResolver::prefix());
        $this->assertMatchesRegularExpression('#^/livewire-[0-9a-f]{8}/update$#', EndpointResolver::updatePath());
        $this->assertMatchesRegularExpression('#^/livewire-[0-9a-f]{8}/upload-file$#', EndpointResolver::uploadPath());
    }

    public function test_the_endpoint_changes_when_the_app_key_changes(): void
    {
        $antes = EndpointResolver::prefix();

        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        $this->assertNotSame(
            $antes,
            EndpointResolver::prefix(),
            'Si esto dejara de cumplirse, el aviso de DEPLOY.md sobre regenerar la APP_KEY sobraría.',
        );
    }
}
