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

];
