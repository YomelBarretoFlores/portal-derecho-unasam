<?php

namespace App\Http\Middleware;

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

        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            trim("img-src 'self' data: blob: {$viteOrigin}"),
            "font-src 'self' https://fonts.bunny.net",
            trim("style-src 'self' 'unsafe-inline' https://fonts.bunny.net {$viteOrigin}"),
            trim("script-src 'self' 'unsafe-inline' 'unsafe-eval' {$viteOrigin}"),
            trim("connect-src 'self' {$viteOrigin} {$viteWebSocketOrigin}"),
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
