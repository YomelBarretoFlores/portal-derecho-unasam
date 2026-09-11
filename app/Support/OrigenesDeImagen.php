<?php

namespace App\Support;

/**
 * Dominios desde los que el navegador tiene permitido cargar imágenes.
 *
 * Existe porque la cabecera Content-Security-Policy declara «img-src 'self'»,
 * y eso bloquea en silencio cualquier imagen alojada fuera del portal. Sin
 * salvedades no se ve una portada servida desde un bucket, ni una pegada como
 * URL de respaldo: el archivo llega bien y el navegador se niega a pintarlo,
 * sin error en la página y sin nada que delate la causa salvo la consola.
 *
 * La lista se calcula, no se escribe a mano. Dos fuentes:
 *
 *   1. El disco de medios configurado. Si es un proveedor externo, su URL
 *      pública entra sola: nadie tiene que acordarse de duplicarla en una
 *      variable aparte.
 *   2. CSP_IMG_HOSTS, para los dominios donde se alojen imágenes de respaldo
 *      mientras las cargas sigan deshabilitadas.
 *
 * La política NO se relaja: nada de «img-src *». Solo entra lo declarado.
 */
class OrigenesDeImagen
{
    /**
     * Orígenes admitidos, en forma «esquema://dominio[:puerto]».
     *
     * No incluye el origen del propio portal: de eso ya se ocupa 'self'.
     *
     * @return array<int, string>
     */
    public static function permitidos(): array
    {
        $candidatos = [
            ...self::deLosDiscosDeMedios(),
            ...self::deLaConfiguracion(),
        ];

        $propio = self::normalizar((string) config('app.url'));

        $origenes = array_filter(
            array_map(self::normalizar(...), $candidatos),
            fn (?string $origen): bool => $origen !== null && $origen !== $propio,
        );

        $origenes = array_values(array_unique($origenes));
        sort($origenes);

        return $origenes;
    }

    /**
     * URL pública de los discos donde viven los medios.
     *
     * Se mira también el disco de conversiones porque puede apuntar a otro
     * sitio: el patrón habitual es guardar los originales en el proveedor y
     * las miniaturas cerca, para no pagar tráfico de salida.
     *
     * @return array<int, string>
     */
    private static function deLosDiscosDeMedios(): array
    {
        $discos = array_filter([
            config('media-library.disk_name'),
            config('media-library.conversions_disk_name'),
        ]);

        return array_values(array_filter(array_map(
            fn (string $disco): ?string => config("filesystems.disks.{$disco}.url"),
            $discos,
        )));
    }

    /** @return array<int, string> */
    private static function deLaConfiguracion(): array
    {
        return (array) config('seguridad.csp.img_hosts', []);
    }

    /**
     * Reduce una URL a su origen, o devuelve null si no sirve.
     *
     * Se descarta todo lo que no sea https, con una excepción: el bucle local,
     * porque en desarrollo un MinIO o un servidor de pruebas va por http. Un
     * origen sin cifrar en producción degradaría a http las imágenes de una
     * página servida por https, y el navegador las bloquearía igual.
     */
    private static function normalizar(string $url): ?string
    {
        $url = trim($url);

        if ($url === '' || $url === '*') {
            return null;
        }

        // Se admite un dominio escueto («medios.unasam.edu.pe») por comodidad
        // de quien rellena la variable; se asume https.
        if (! str_contains($url, '://')) {
            $url = 'https://'.$url;
        }

        $partes = parse_url($url);

        $esquema = strtolower((string) ($partes['scheme'] ?? ''));
        $dominio = (string) ($partes['host'] ?? '');

        if ($dominio === '' || ! in_array($esquema, ['http', 'https'], true)) {
            return null;
        }

        $esLocal = in_array(trim($dominio, '[]'), ['localhost', '127.0.0.1', '::1'], true);

        if ($esquema === 'http' && ! $esLocal) {
            return null;
        }

        return $esquema.'://'.$dominio.(isset($partes['port']) ? ':'.$partes['port'] : '');
    }

    /**
     * ¿Se verá esta imagen en el sitio, o la bloqueará el navegador?
     *
     * Lo usa el panel para rechazar en el formulario una URL que, de guardarse,
     * dejaría un hueco silencioso en la página.
     */
    public static function admite(string $url): bool
    {
        $url = trim($url);

        // Las rutas internas y los datos embebidos los cubre «'self' data:».
        if ($url === '' || str_starts_with($url, 'data:') || (str_starts_with($url, '/') && ! str_starts_with($url, '//'))) {
            return true;
        }

        $origen = self::normalizar($url);

        return $origen !== null
            && ($origen === self::normalizar((string) config('app.url')) || in_array($origen, self::permitidos(), true));
    }
}
