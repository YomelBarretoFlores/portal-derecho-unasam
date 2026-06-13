<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                    ->maxLength(255),
                Toggle::make('is_admin')
                    ->label('Administrador')
                    ->helperText('Permite el acceso al panel /admin.')
                    ->default(true)
                    ->disabled(fn (?User $record): bool => $record?->id === auth()->id())
                    ->dehydrated(),
            ]);
    }
}
