<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password as preguntarContrasena;

/**
 * Devuelve el acceso al panel a quien administre el servidor.
 *
 * Hacía falta porque el portal se quedó sin puerta de atrás. La siembra crea
 * la cuenta inicial una sola vez: si el usuario ya existe, NO le toca la
 * contraseña, así que cambiar ADMIN_PASSWORD en el .env y volver a desplegar
 * no sirve de nada. Y la recuperación por correo no existe mientras no haya
 * servidor de correo configurado.
 *
 * El resultado era que una cuenta cuya contraseña nadie recuerda deja el panel
 * cerrado para siempre, con el contenido dentro y sin forma de entrar. Le pasó
 * al portal de la UNASAM.
 *
 * La contraseña se pide por teclado y no se acepta como argumento: un
 * argumento queda escrito en el historial del intérprete de órdenes y, en
 * muchos servidores, visible en la lista de procesos para cualquier otro
 * usuario mientras el comando corre.
 */
class RestablecerAdministrador extends Command
{
    protected $signature = 'admin:restablecer
                            {correo? : Correo de la cuenta; si no existe, se crea}
                            {--nombre= : Nombre para mostrar, solo al crear una cuenta nueva}';

    protected $description = 'Restablece la contraseña del administrador, o crea la cuenta si no existe';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Este comando pide la contraseña por teclado, así que necesita una sesión interactiva.');

            return self::FAILURE;
        }

        $correo = (string) ($this->argument('correo') ?: $this->ask('Correo de la cuenta'));

        if (! Validator::make(['correo' => $correo], ['correo' => ['required', 'email']])->passes()) {
            $this->error("«{$correo}» no es un correo válido.");

            return self::FAILURE;
        }

        $usuario = User::where('email', $correo)->first();

        if ($usuario) {
            $this->line("Cuenta encontrada: {$usuario->name} <{$usuario->email}>");
            $this->line('Rol actual: '.(User::ROLES[$usuario->role] ?? 'sin rol asignado'));
        } else {
            $this->line("No existe ninguna cuenta con «{$correo}»; se va a crear.");

            $listado = User::query()->select('email', 'role')->orderBy('email')->get();
            if ($listado->isNotEmpty()) {
                $this->newLine();
                $this->line('Cuentas que ya existen, por si el correo era otro:');
                foreach ($listado as $otra) {
                    $this->line(sprintf('  %-40s %s', $otra->email, User::ROLES[$otra->role] ?? 'sin rol'));
                }
                $this->newLine();

                if (! $this->confirm("¿Crear igualmente una cuenta nueva para «{$correo}»?", false)) {
                    $this->line('No se ha cambiado nada.');

                    return self::SUCCESS;
                }
            }
        }

        $this->newLine();
        $this->line('La contraseña no se verá al escribirla. Mínimo 12 caracteres,');
        $this->line('con mayúscula, minúscula, número y un signo.');

        $contrasena = preguntarContrasena('Contraseña nueva');
        $repetida = preguntarContrasena('Repítala');

        if ($contrasena !== $repetida) {
            $this->error('Las dos contraseñas no coinciden. No se ha cambiado nada.');

            return self::FAILURE;
        }

        $validacion = Validator::make(['password' => $contrasena], [
            'password' => ['required', 'string', 'min:12', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ]);

        if ($validacion->fails()) {
            $this->error('Esa contraseña no cumple los requisitos:');
            foreach ($validacion->errors()->all() as $error) {
                $this->line('  - '.$error);
            }

            return self::FAILURE;
        }

        if ($usuario) {
            $usuario->forceFill([
                'password' => $contrasena,
                'role' => User::ROLE_SUPER_ADMIN,
                'is_admin' => true,
            ])->save();

            $this->newLine();
            $this->info("Contraseña cambiada. {$usuario->email} entra como superadministrador.");
        } else {
            $usuario = User::create([
                'name' => (string) ($this->option('nombre') ?: $this->ask('Nombre para mostrar', 'Administrador')),
                'email' => $correo,
                'password' => $contrasena,
                'role' => User::ROLE_SUPER_ADMIN,
                'is_admin' => true,
            ]);

            $this->newLine();
            $this->info("Cuenta creada. {$usuario->email} entra como superadministrador.");
        }

        $this->line('Entre en /admin y cámbiela desde su perfil si la escribió alguien más.');

        return self::SUCCESS;
    }
}
