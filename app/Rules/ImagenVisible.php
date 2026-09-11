<?php

namespace App\Rules;

use App\Support\OrigenesDeImagen;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * La imagen tiene que poder verse, no solo guardarse.
 *
 * Una URL de otro dominio pasa SafeUrl sin problema —es https y está bien
 * formada— y aun así el navegador se niega a pintarla, porque la cabecera
 * Content-Security-Policy solo admite los orígenes declarados. El resultado
 * es el peor posible para quien administra: el formulario dice «guardado»,
 * la página no muestra nada, y no hay ningún error que seguir.
 *
 * Esta regla lo convierte en un mensaje en el formulario, donde aún se puede
 * hacer algo al respecto.
 */
class ImagenVisible implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value) || OrigenesDeImagen::admite((string) $value)) {
            return;
        }

        $permitidos = OrigenesDeImagen::permitidos();

        $fail($permitidos === []
            ? 'Esa imagen está alojada en otro dominio y el navegador la bloquearía: la página quedaría con un hueco. Suba el archivo, use una ruta del propio portal, o pida que añadan ese dominio a CSP_IMG_HOSTS.'
            : 'Esa imagen está alojada en un dominio que el navegador bloquearía. Dominios admitidos hoy: '.implode(', ', $permitidos).'.');
    }
}
