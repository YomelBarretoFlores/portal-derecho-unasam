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
use Illuminate\Validation\ValidationException;

#[Fillable(['name', 'email', 'password', 'role', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_EDITOR = 'editor';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Superadministrador',
        self::ROLE_EDITOR => 'Editor',
    ];

    /**
     * Autoriza el acceso al panel Filament.
     *
     * Filament, fuera del entorno local, exige este método; de lo contrario
     * devuelve 403. Durante la migración se conserva `is_admin` únicamente como
     * compatibilidad; las nuevas autorizaciones se basan en roles explícitos.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, array_keys(self::ROLES), true)
            || ($this->role === null && (bool) $this->is_admin);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR;
    }

    protected static function booted(): void
    {
        static::updating(function (self $user): void {
            if ($user->getOriginal('role') === self::ROLE_SUPER_ADMIN
                && $user->role !== self::ROLE_SUPER_ADMIN
                && static::query()->where('role', self::ROLE_SUPER_ADMIN)->count() <= 1) {
                throw ValidationException::withMessages(['role' => 'Debe existir al menos un superadministrador.']);
            }
        });

        static::deleting(function (self $user): void {
            if ($user->role === self::ROLE_SUPER_ADMIN
                && static::query()->where('role', self::ROLE_SUPER_ADMIN)->count() <= 1) {
                throw ValidationException::withMessages(['user' => 'No se puede eliminar al último superadministrador.']);
            }
        });
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
