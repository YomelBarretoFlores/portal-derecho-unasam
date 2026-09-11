<?php

namespace App\Filament\Forms;

use App\Rules\ImagenVisible;
use App\Rules\SafeUrl;
use Filament\Forms\Components\TextInput;

/**
 * El campo «URL pública de respaldo» y el texto que acompaña a las cargas.
 *
 * Está en un solo sitio porque son siete campos que dicen lo mismo, y cuando
 * el texto se escribe siete veces acaba diciendo siete cosas distintas. De
 * hecho ya pasaba: unos campos explicaban por qué la carga estaba en gris y
 * otros se quedaban en gris sin decir nada, que es la peor versión posible
 * para quien administra y no sabe si el fallo es suyo.
 */
class UrlDeRespaldo
{
    /**
     * Campo de respaldo para una IMAGEN.
     *
     * Valida además que el dominio esté autorizado por la política de
     * seguridad: una imagen de un dominio no declarado se guardaría sin
     * queja y el navegador se negaría a pintarla.
     */
    public static function imagen(string $campo, string $etiqueta): TextInput
    {
        return TextInput::make($campo)
            ->label($etiqueta)
            ->rule(new SafeUrl)
            ->rule(new ImagenVisible)
            ->columnSpanFull()
            ->helperText(self::ayuda(
                'Pegue la dirección del archivo de imagen —termina en .jpg, .png o .webp—, '
                .'no el enlace a una publicación de Instagram o Facebook.',
            ));
    }

    /** Campo de respaldo para un ARCHIVO descargable (PDF, documento). */
    public static function archivo(string $campo, string $etiqueta): TextInput
    {
        return TextInput::make($campo)
            ->label($etiqueta)
            ->rule(new SafeUrl)
            ->columnSpanFull()
            ->helperText(self::ayuda());
    }

    /**
     * Texto del campo de carga, que explica por qué está deshabilitado.
     *
     * @param  string  $cuandoSePuede  qué decir cuando las cargas funcionan
     */
    public static function textoDeCarga(string $cuandoSePuede): string
    {
        return config('media.uploads_enabled')
            ? $cuandoSePuede
            : 'Este servidor todavía no guarda archivos subidos: se perderían en la próxima '
              .'actualización del portal. Mientras el área de sistemas no habilite el '
              .'almacenamiento, use el campo de dirección web que aparece justo debajo.';
    }

    private static function ayuda(string $extra = ''): string
    {
        $base = config('media.uploads_enabled')
            ? 'Solo se usa si no se sube ningún archivo.'
            : 'Por ahora es la única forma de poner una imagen aquí.';

        return trim($base.' '.$extra);
    }
}
