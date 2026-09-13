<?php

namespace App\Http\Middleware;

use App\Support\OrigenesDeImagen;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        [$viteOrigin, $viteWebSocketOrigin] = $this->viteDevelopmentOrigins();

        // Dominios desde los que se sirven imágenes del portal: el bucket de
        // medios, si lo hay, y los declarados en CSP_IMG_HOSTS. Sin esto, una
        // portada alojada fuera se descarga bien y el navegador se niega a
        // pintarla, sin más rastro que un aviso en la consola.
        $origenesDeImagen = implode(' ', array_filter([
            ...OrigenesDeImagen::permitidos(),
            $viteOrigin,
        ]));

        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            trim("img-src 'self' data: blob: {$origenesDeImagen}"),
            "font-src 'self' https://fonts.bunny.net",
            trim("style-src 'self' 'unsafe-inline' https://fonts.bunny.net {$viteOrigin}"),
            trim("script-src 'self' 'unsafe-inline' 'unsafe-eval' {$viteOrigin}"),
            trim("connect-src 'self' {$viteOrigin} {$viteWebSocketOrigin}"),
            // El componente de carga de archivos del panel procesa cada archivo
            // en un Web Worker que crea al vuelo, con una URL «blob:». Sin esta
            // línea el navegador lo bloquea y el recuadro de «arrastra tu
            // archivo» se queda como un campo básico que no sube nada: no hay
            // mensaje de error, solo deja de funcionar.
            //
            // Lo encontró el área de sistemas de la UNASAM depurando el panel
            // en el servidor de la universidad.
            "worker-src 'self' blob:",
        ]));

        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    /** @return array{0: string, 1: string} */
    private function viteDevelopmentOrigins(): array
    {
        if (! app()->environment(['local', 'testing']) || ! Vite::isRunningHot()) {
            return ['', ''];
        }

        $hotUrl = trim((string) file_get_contents(Vite::hotFile()));
        $scheme = parse_url($hotUrl, PHP_URL_SCHEME);
        $host = parse_url($hotUrl, PHP_URL_HOST);
        $port = parse_url($hotUrl, PHP_URL_PORT);

        if (! in_array($scheme, ['http', 'https'], true)
            || ! is_string($host)
            || ! in_array(trim($host, '[]'), ['localhost', '127.0.0.1', '::1'], true)) {
            return ['', ''];
        }

        $origin = $scheme.'://'.$host.($port ? ':'.$port : '');
        $webSocketOrigin = ($scheme === 'https' ? 'wss' : 'ws').'://'.$host.($port ? ':'.$port : '');

        return [$origin, $webSocketOrigin];
    }
}
