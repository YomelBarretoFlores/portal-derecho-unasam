<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Correos con acceso al panel /admin
    |--------------------------------------------------------------------------
    |
    | Lista blanca de correos autorizados a entrar al panel Filament
    | (ver App\Models\User::canAccessPanel). Se define por entorno como una
    | cadena separada por comas; en producción se configura la variable
    | ADMIN_EMAILS en el servidor (Render). El valor por defecto coincide con
    | el usuario que crea AdminUserSeeder.
    |
    */

    'emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ADMIN_EMAILS', 'barretofloresyomeljair@gmail.com')),
    ))),

];
