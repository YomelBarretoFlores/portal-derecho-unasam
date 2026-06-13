<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                // No puedes eliminar tu propia cuenta.
                ->hidden(fn (User $record): bool => $record->id === auth()->id())
                // No puedes eliminar al último administrador.
                ->before(function (DeleteAction $action, User $record): void {
                    if ($record->is_admin && User::where('is_admin', true)->count() <= 1) {
                        Notification::make()
                            ->danger()
                            ->title('No puedes eliminar al último administrador.')
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
