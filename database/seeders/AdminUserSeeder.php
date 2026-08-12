<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('admin.bootstrap_name');
        $email = config('admin.bootstrap_email');
        $password = config('admin.bootstrap_password');

        if (blank($name) || blank($email) || blank($password)) {
            if (app()->environment('production')) {
                throw new \RuntimeException('Define ADMIN_NAME, ADMIN_EMAIL y ADMIN_PASSWORD antes de ejecutar seeders en producción.');
            }

            $this->command?->warn('Administrador omitido: define ADMIN_NAME, ADMIN_EMAIL y ADMIN_PASSWORD para crearlo.');

            return;
        }

        Validator::make(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:12', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ])->validate();

        if ($user = User::where('email', $email)->first()) {
            if (! $user->isSuperAdmin()) {
                $user->forceFill(['role' => User::ROLE_SUPER_ADMIN, 'is_admin' => true])->save();
            }

            $this->command?->info("El usuario admin ya existe: {$email}");

            return;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => User::ROLE_SUPER_ADMIN,
            'is_admin' => true,
        ]);

        $this->command?->info("Usuario superadministrador creado: {$email}");
    }
}
