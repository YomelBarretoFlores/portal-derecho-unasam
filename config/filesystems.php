<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        /*
         * Los dos discos del portal en modo proveedor externo.
         *
         * Existen por separado, y no como un solo bucket con carpetas, porque
         * sus exigencias son opuestas: los medios TIENEN que ser legibles por
         * cualquiera —son las portadas y los PDF que ve el público—, y los
         * manuscritos NO DEBEN serlo nunca. Con un único bucket, un fallo en
         * una regla de acceso dejaría los originales inéditos de los autores
         * al alcance de quien adivine la ruta.
         *
         * Sirve cualquier proveedor compatible con S3: Cloudflare R2, el
         * almacenamiento de objetos de Neon, MinIO, AWS S3 o un servidor
         * propio. Lo que cambia es el endpoint, no el código.
         */
        'medios' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            // URL pública de lectura del bucket. También alimenta la cabecera
            // Content-Security-Policy: sin ella el navegador bloquea las
            // imágenes por venir de otro dominio. Véase SecurityHeaders.
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'manuscritos' => [
            'driver' => 's3',
            'key' => env('AWS_SUBMISSIONS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
            'secret' => env('AWS_SUBMISSIONS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
            'region' => env('AWS_SUBMISSIONS_DEFAULT_REGION', env('AWS_DEFAULT_REGION')),
            'bucket' => env('AWS_SUBMISSIONS_BUCKET'),
            'endpoint' => env('AWS_SUBMISSIONS_ENDPOINT', env('AWS_ENDPOINT')),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            // Sin «url»: los manuscritos se sirven siempre a través de una ruta
            // autenticada del panel, nunca por enlace directo al bucket.
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
