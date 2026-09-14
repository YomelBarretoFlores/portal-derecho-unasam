<?php

namespace App\Filament\Resources\Revistas\Pages;

use App\Filament\Actions\PreviewActions;
use App\Filament\Resources\Revistas\RevistaResource;
use App\Models\Setting;
use Filament\Resources\Pages\EditRecord;

class EditRevista extends EditRecord
{
    protected static string $resource = RevistaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewActions::preview(),
            PreviewActions::published(),
        ];
    }

    /*
     * «Recibir manuscritos ahora» no es una columna de la tabla, es un ajuste.
     *
     * Se guarda así a propósito, para no añadir una columna a «revistas». En
     * PostgreSQL detrás de un pooler, alterar una tabla que el sitio consulta
     * en cada página deja las consultas preparadas apuntando a la forma vieja
     * y devuelve «cached plan must not change result type» en todo el portal
     * hasta que alguien descarta los planes. Ya pasó una vez, y el servidor de
     * la universidad no ejecuta el comando que los limpia.
     *
     * Un ajuste no cambia la forma de ninguna tabla, así que la actualización
     * no puede tumbar nada.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recepcion_abierta'] = (bool) Setting::get('revista_recepcion_abierta', true);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /*
         * Solo se escribe si el campo vino de verdad en el formulario.
         *
         * Filament no envía los campos deshabilitados al guardar, y este lo
         * está mientras el servidor no pueda guardar manuscritos. Sin esta
         * comprobación, el valor ausente se leía como «desactivado» y guardar
         * cualquier otro cambio de la ficha —el nombre corto, una red social—
         * cerraba la recepción sin que nadie lo pidiera. Lo peor es que no
         * dejaba rastro: el panel simplemente amanecía cerrado.
         */
        if (array_key_exists('recepcion_abierta', $data)) {
            Setting::set('revista_recepcion_abierta', empty($data['recepcion_abierta']) ? '0' : '1');
            unset($data['recepcion_abierta']);
        }

        return $data;
    }
}
