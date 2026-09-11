<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Da mascota a la página de error en instalaciones que ya existían.
 *
 * Misma razón que en la del hero: el archivo viaja con la aplicación, pero
 * SettingSeeder solo crea claves que falten y en un portal ya desplegado no
 * vuelve a pasar por aquí.
 *
 * Va en su propia clave, y no reutilizando la del hero, porque las dos
 * ilustraciones no son intercambiables: en el 404 la mascota cae desde arriba
 * y la versión volando encaja; la de pie, cayendo del cielo, no.
 *
 * Solo escribe si la clave falta o está vacía, así que no pisa una decisión
 * tomada desde el panel.
 */
return new class extends Migration
{
    private const CLAVE = 'error404_mascota_url';

    private const VALOR = '/img/mascota-volando.webp';

    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $fila = DB::table('settings')->where('clave', self::CLAVE)->first();

        if ($fila === null) {
            DB::table('settings')->insert([
                'clave' => self::CLAVE,
                'valor' => self::VALOR,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif (blank($fila->valor)) {
            DB::table('settings')->where('clave', self::CLAVE)->update([
                'valor' => self::VALOR,
                'updated_at' => now(),
            ]);
        } else {
            return;
        }

        /*
         * Imprescindible: los ajustes se sirven de una caché que solo invalidan
         * los eventos de Eloquent, y aquí se ha escrito con DB::table(). Sin
         * esto la mascota aparecería en un servidor recién arrancado y no en
         * uno con la caché ya caliente —exactamente el desconcierto que hubo
         * con la del hero.
         */
        Setting::olvidarCache();
    }

    public function down(): void
    {
        DB::table('settings')->where('clave', self::CLAVE)->delete();
        Setting::olvidarCache();
    }
};
