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
            /*
             * Que falten las variables solo es un problema cuando NO hay a quién
             * dejar entrar. Si el portal ya tiene un superadministrador, no hay
             * nada que crear y exigirlas sobra.
             *
             * Antes se lanzaba la excepción sin mirar eso, así que un portal
             * instalado hace meses —con su cuenta creada y en uso— escupía un
             * volcado de pila en el registro de errores en CADA despliegue. Un
             * error que sale siempre y nunca significa nada es peor que ninguno:
             * entierra los que sí importan y enseña a no leer el registro.
             */
            if (User::query()->where('role', User::ROLE_SUPER_ADMIN)->exists()) {
                $this->command?->info('Ya hay un superadministrador; no hace falta crear ninguno.');

                return;
            }

            if (app()->environment('production')) {
                throw new \RuntimeException('No existe ningún administrador y faltan ADMIN_NAME, ADMIN_EMAIL y ADMIN_PASSWORD: nadie podría entrar a /admin.');
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
