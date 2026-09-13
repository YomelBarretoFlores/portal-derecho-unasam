<?php

return [

    'csp' => [

        /*
        |----------------------------------------------------------------------
        | Dominios externos autorizados para servir imágenes
        |----------------------------------------------------------------------
        |
        | La cabecera Content-Security-Policy declara «img-src 'self'», así que
        | el navegador bloquea cualquier imagen alojada fuera del portal. Esta
        | lista son las excepciones, separadas por comas:
        |
        |     CSP_IMG_HOSTS=medios.unasam.edu.pe,repositorio.unasam.edu.pe
        |
        | Vacía por defecto, y a propósito: cada dominio aquí es un sitio más
        | desde el que alguien podría colar una imagen en las páginas del
        | portal. Añada solo los que hagan falta, nunca «*».
        |
        | El dominio del bucket de medios NO hay que ponerlo aquí: se deduce
        | solo de AWS_URL. Véase App\Support\OrigenesDeImagen.
        |
        */
        'img_hosts' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CSP_IMG_HOSTS', '')),
        ))),

    ],

    /*
    |--------------------------------------------------------------------------
    | Proxies de confianza
    |--------------------------------------------------------------------------
    |
    | IPs o rangos CIDR del proxy que tiene delante el portal —Cloudflare, un
    | balanceador, nginx—, separados por comas. Sirven para que la aplicación
    | sepa el dominio y el esquema reales de la petición en vez de los del
    | proxy.
    |
    | Vacío es una respuesta válida: significa «no hay proxy delante», que es
    | el caso de una instalación directa sobre el servidor. Solo hay que
    | rellenarlo si el portal se sirve a través de algo.
    |
    | Se lee desde config y no con env() en bootstrap/app.php a propósito:
    | cuando la configuración está cacheada —y en producción lo está—, las
    | llamadas a env() fuera de los ficheros de config devuelven null. Una
    | comprobación escrita allí con env() no se ejecuta nunca, y da una
    | falsa sensación de estar protegiendo algo.
    |
    */
    'proxies_de_confianza' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('TRUSTED_PROXIES', '')),
    ))),

];
