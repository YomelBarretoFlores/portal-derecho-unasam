<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Declara los proxies que hay delante del portal, si los hay.
 *
 * Vive aparte de AppServiceProvider por dos razones. La primera es que así se
 * puede comprobar sin depender del arranque de la aplicación: un test que tenga
 * que colocar la configuración ANTES de que arranque Laravel es frágil y se
 * rompe según el orden en que corran los demás.
 *
 * La segunda es que la lógica merece explicarse en un sitio. Antes vivía en
 * bootstrap/app.php y exigía que TRUSTED_PROXIES estuviera relleno en
 * producción, lanzando una excepción si no. Estaba mal dos veces: la
 * comprobación usaba env(), que con la configuración cacheada —como va
 * producción— devuelve null fuera de los ficheros de config, así que no llegaba
 * a ejecutarse nunca; y cuando sí saltaba, tumbaba el portal entero, siendo que
 * vacío no es un error sino «no hay proxy delante».
 */
class ProxiesDeConfianza
{
    /**
     * Cabeceras que se aceptan del proxy: de quién viene, para qué dominio,
     * por qué puerto y con qué esquema. Sin ellas el portal se cree servido
     * desde el propio servidor y compone mal sus enlaces.
     */
    private const CABECERAS = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO;

    public static function aplicar(): void
    {
        $proxies = array_values(array_filter((array) config('seguridad.proxies_de_confianza', [])));

        if (in_array('*', $proxies, true)) {
            /*
             * Confiar en cualquier proxy permite a cualquiera falsear su IP con
             * una cabecera inventada, y con ella saltarse los límites por IP.
             * Se descarta el valor, pero sin dejar el sitio caído por ello.
             */
            Log::warning('TRUSTED_PROXIES contiene «*» y se ignora: ponga las IPs o rangos CIDR del proxy.');

            return;
        }

        if ($proxies === []) {
            return;
        }

        Request::setTrustedProxies($proxies, self::CABECERAS);
    }
}
