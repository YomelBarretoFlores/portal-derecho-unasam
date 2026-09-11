<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Deja puesta la mascota del hero en instalaciones que ya existían.
 *
 * El archivo viaja con la aplicación, pero el ajuste que lo señala se creó
 * vacío en el despliegue anterior. SettingSeeder no lo arregla: solo crea las
 * claves que faltan, y esta ya está.
 *
 * Se hace aquí y no con un valor por defecto en el código porque el panel debe
 * poder quitarla. Con un valor por defecto, vaciar el campo no serviría de
 * nada: volvería a aparecer en la siguiente carga.
 *
 * Solo rellena lo que esté vacío, así que no pisa una decisión del panel ni
 * vuelve a poner la mascota si alguien la quitó.
 */
return new class extends Migration
{
    private const VALORES = [
        'home_hero_mascota_url' => '/img/mascota-derecho.webp',
        'home_hero_mascota_alt' => 'Mascota de la Facultad de Derecho: una coneja con casco romano, capa y camiseta de UNASAM Derecho',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $escrito = false;

        foreach (self::VALORES as $clave => $valor) {
            $fila = DB::table('settings')->where('clave', $clave)->first();

            if ($fila === null) {
                DB::table('settings')->insert([
                    'clave' => $clave,
                    'valor' => $valor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $escrito = true;

                continue;
            }

            if (blank($fila->valor)) {
                DB::table('settings')->where('clave', $clave)->update([
                    'valor' => $valor,
                    'updated_at' => now(),
                ]);
                $escrito = true;
            }
        }

        /*
         * Los ajustes se sirven desde una caché que se guarda para siempre y
         * que solo invalidan los eventos del modelo. Esta migración escribe con
         * el constructor de consultas —lo correcto: una migración no debe
         * depender de un modelo que quizá cambie—, así que esos eventos no se
         * disparan y hay que vaciar la caché a mano.
         *
         * Sin esto, un servidor que lleve tiempo en marcha aplica la migración
         * y sigue mostrando el valor anterior hasta que algo más toque los
         * ajustes. Pasó en desarrollo: en el servidor se veía la mascota, y en
         * la máquina de quien desarrolla no, con la misma base de datos.
         */
        if ($escrito) {
            Setting::olvidarCache();
        }
    }

    public function down(): void
    {
        // No se revierte: vaciar estas claves borraría una decisión editorial
        // posterior sin forma de distinguirla del valor que puso la migración.
    }
};
