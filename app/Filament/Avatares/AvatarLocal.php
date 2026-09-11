<?php

namespace App\Filament\Avatares;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Avatar generado en el propio servidor, como SVG embebido en un data: URI.
 *
 * El proveedor que trae Filament de serie pide la imagen a un servicio externo
 * (`https://ui-avatars.com/api/?name=…`). Eso tenía dos consecuencias aquí:
 *
 * 1. **No se veía.** La cabecera Content-Security-Policy del proyecto declara
 *    `img-src 'self' data: blob:`, así que el navegador bloqueaba la petición y
 *    el panel mostraba el icono de imagen rota. La CSP está bien como está; el
 *    proveedor era el que no encajaba.
 * 2. **Enviaba el nombre del usuario a un tercero** en cada carga del panel, sin
 *    que nadie lo hubiera consentido. Para una institución pública eso es una
 *    cesión de datos que no aporta nada a cambio.
 *
 * Dibujamos las iniciales sobre el navy institucional. Sin petición externa, sin
 * latencia y sin depender de que ese servicio siga existiendo.
 */
class AvatarLocal implements AvatarProvider
{
    public function get(Model $record): string
    {
        $nombre = trim((string) ($record->getAttribute('name') ?? ''));

        $iniciales = Str::of($nombre)
            ->squish()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $parte): string => Str::upper(Str::substr($parte, 0, 1)))
            ->implode('');

        if ($iniciales === '') {
            $iniciales = '·';
        }

        // navy-900 (#17335c) sobre blanco: el mismo color estructural del sitio.
        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64" role="img" aria-label="{$iniciales}">
                <rect width="64" height="64" fill="#17335c"/>
                <text x="32" y="32" fill="#ffffff" font-family="system-ui, sans-serif" font-size="26"
                      font-weight="600" text-anchor="middle" dominant-baseline="central">{$iniciales}</text>
            </svg>
            SVG;

        return 'data:image/svg+xml;base64,'.base64_encode(preg_replace('/\s+/', ' ', $svg) ?? $svg);
    }
}
