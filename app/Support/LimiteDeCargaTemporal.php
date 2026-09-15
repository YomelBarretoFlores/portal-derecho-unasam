<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;

/**
 * Sube el tope de Livewire hasta lo que el portal dice admitir.
 *
 * Todo archivo que se sube desde el panel pasa antes por un almacén temporal
 * de Livewire, y ese almacén tiene su propio límite de tamaño: 12 MB de
 * fábrica. El panel, en cambio, anuncia PDF de hasta 20 MB y manuscritos de
 * hasta 15 MB.
 *
 * Entre los 12 MB de Livewire y los 20 MB del panel hay una franja en la que
 * el archivo se rechaza con un mensaje de validación genérico, antes de que
 * ningún límite nuestro llegue a aplicarse. Quien sube una resolución
 * escaneada de 14 MB —que es justo el tamaño típico de un escaneo— ve que el
 * formulario no lo admite y no entiende por qué, porque el propio campo le
 * acaba de decir que el máximo son 20 MB.
 *
 * Así que el tope se calcula a partir de nuestros propios límites y hay un
 * único sitio donde cambiarlos: el .env. Esto no relaja nada, porque cada
 * campo sigue aplicando después su límite real y su lista de tipos
 * permitidos; solo deja de estorbar antes de tiempo.
 */
class LimiteDeCargaTemporal
{
    public static function aplicar(): void
    {
        Config::set('livewire.temporary_file_upload.rules', [
            'required',
            'file',
            'max:'.self::maximoEnKb(),
        ]);
    }

    /** El mayor de los tamaños que el portal promete admitir, en kilobytes. */
    public static function maximoEnKb(): int
    {
        return max(
            (int) config('media.max_image_kb'),
            (int) config('media.max_pdf_kb'),
            (int) config('submissions.manuscript_max_kb'),
            (int) config('submissions.attachment_max_kb'),
            1,
        );
    }
}
