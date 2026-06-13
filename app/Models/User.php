<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Autoriza el acceso al panel Filament.
     *
     * Filament, fuera del entorno local, exige este método; de lo contrario
     * devuelve 403. El control principal es el flag is_admin (gestionable desde
     * el panel); la lista blanca config('admin.emails') queda como respaldo
     * "break-glass" para que el dueño nunca quede bloqueado.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin
            || in_array($this->email, config('admin.emails', []), true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
}
