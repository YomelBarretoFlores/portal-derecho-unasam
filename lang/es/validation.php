<?php

/*
|--------------------------------------------------------------------------
| Mensajes de validación en castellano
|--------------------------------------------------------------------------
|
| Solo están las claves que el portal necesita de verdad. Laravel trae sus
| mensajes en inglés y, cuando falta la traducción, no falla: enseña el texto
| en inglés con el nombre interno del campo. En un panel que usa personal
| administrativo, eso es lo mismo que no decir nada.
|
*/

return [

    /*
     * Este es el que importa.
     *
     * Cuando una carga falla —porque el servidor web corta el envío por
     * tamaño, o porque se pierde la conexión a medias—, sin esta línea el
     * panel mostraba:
     *
     *     The data.archivo.f62d80ec-ef13-4afd-8508-64a02aec3577 failed to upload.
     *
     * En inglés, con el identificador interno del campo, y sin decir qué
     * hacer. Quien lo ve solo sabe que algo no funcionó.
     */
    'uploaded' => 'No se pudo subir el archivo. Suele ser porque pesa más de lo que admite el servidor: pruebe con uno más ligero, o avise al área de sistemas.',

    'max' => [
        'file' => 'El archivo no puede pesar más de :max kilobytes.',
    ],

    'mimes' => 'El archivo debe ser de tipo: :values.',
    'mimetypes' => 'El archivo debe ser de tipo: :values.',
    'required' => 'Este campo es obligatorio.',
    'image' => 'El archivo debe ser una imagen.',
    'url' => 'Escriba una dirección web completa, empezando por https://',
    'email' => 'Escriba un correo electrónico válido.',

];
