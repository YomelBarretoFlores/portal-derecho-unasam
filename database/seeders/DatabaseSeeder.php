<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Siembra completa: la base institucional más el usuario administrador.
 *
 * En el servidor no se llama a este seeder, sino a los dos por separado
 * (véase docker/entrypoint.sh). El motivo: el administrador exige ADMIN_NAME,
 * ADMIN_EMAIL y ADMIN_PASSWORD, y si faltan hay que avisar sin impedir que el
 * sitio arranque con su contenido. Llamados juntos, un fallo al crear la
 * cuenta dejaría el portal en blanco por una razón que no tiene que ver con
 * el contenido.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ContenidoInstitucionalSeeder::class);
        $this->call(AdminUserSeeder::class);
    }
}
