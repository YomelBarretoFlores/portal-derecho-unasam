<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('En edición, déjalo en blanco para no cambiarla.')
                    ->minLength(12)
                    ->rules(['regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'])
                    ->maxLength(255),
                Select::make('role')
                    ->label('Rol')
                    ->options(User::ROLES)
                    ->required()
                    ->default(User::ROLE_EDITOR)
                    ->disabled(fn (?User $record): bool => $record?->id === auth()->id())
                    ->dehydrated(),
            ]);
    }
}
