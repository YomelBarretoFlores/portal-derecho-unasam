<?php

namespace App\Filament\Forms;

use App\Enums\EditorialStatus;
use Filament\Forms\Components\Select;

class EditorialStatusSelect
{
    public static function make(): Select
    {
        return Select::make('estado_editorial')
            ->label('Estado editorial')
            ->options(EditorialStatus::options())
            ->default(EditorialStatus::Draft->value)
            ->required()
            ->native(false)
            ->live()
            ->helperText('Solo “Publicado” hace visible el contenido en el sitio público. Usa Vista previa para revisar los demás estados.');
    }
}
