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
        Setting::set('revista_recepcion_abierta', ! empty($data['recepcion_abierta']) ? '1' : '0');
        unset($data['recepcion_abierta']);

        return $data;
    }
}
