<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('email')->label('Correo')->searchable(),
                TextColumn::make('role')->label('Rol')->badge()->formatStateUsing(fn (?string $state): string => User::ROLES[$state] ?? 'Sin acceso'),
                TextColumn::make('created_at')->label('Creado')->date()->sortable(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
            ]);
        // Sin acciones masivas de borrado: evita eliminar a todos los admins de golpe.
    }
}
