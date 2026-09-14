<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Image\Image;
use Tests\TestCase;

/**
 * El comando de verificación tiene que fallar cuando las cargas fallan.
 *
 * Antes escribía un fichero de texto en el disco, veía que se escribía y leía
 * bien, y daba todo por correcto —mientras cada carga desde el panel devolvía
 * error 500 en el servidor de la universidad—. Escribir un .txt no toca la
 * biblioteca de imágenes; subir un retrato sí, porque de cada imagen se genera
 * una versión reducida, y sin GD esa generación revienta.
 *
 * Un comando que dice «todo bien» mientras nada funciona es peor que no tener
 * comando: manda a buscar el fallo al sitio equivocado.
 */
class VerificacionDeCargasTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_check_exercises_image_conversion_and_not_only_the_disk(): void
    {
        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('Procesado de imágenes')
            ->expectsOutputToContain('Miniatura generada correctamente.');
    }

    public function test_the_check_names_the_image_extension_it_found(): void
    {
        // Saber cuál está cargada es la mitad del diagnóstico: con IMAGE_DRIVER
        // apuntando a una extensión que no está instalada, las cargas fallan
        // aunque la otra sí lo esté.
        $motor = extension_loaded('imagick') ? 'imagick' : 'gd';

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain("Extensión disponible: {$motor}");
    }

    public function test_the_conversion_really_resizes_the_image(): void
    {
        // Duplica lo que hace el comando, para que un cambio que dejara de
        // comprobar el tamaño no pasara desapercibido.
        $origen = tempnam(sys_get_temp_dir(), 'test').'.jpg';
        $destino = tempnam(sys_get_temp_dir(), 'test').'.jpg';

        $lienzo = imagecreatetruecolor(120, 90);
        imagejpeg($lienzo, $origen);
        imagedestroy($lienzo);

        Image::load($origen)->width(40)->save($destino);

        [$ancho] = getimagesize($destino);

        @unlink($origen);
        @unlink($destino);

        $this->assertSame(40, $ancho);
    }

    public function test_the_check_detects_a_missing_media_table(): void
    {
        // El 500 al adjuntar un archivo NO puede venir de un fallo de permisos
        // en el disco: la biblioteca de medios captura ese caso y devuelve
        // false sin excepción. Sí viene de aquí, que es el único paso del
        // camino que un registro de solo texto no recorre.
        Schema::drop('media');

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('La tabla «media» no existe')
            ->assertFailed();
    }

    public function test_the_check_detects_a_half_migrated_media_table(): void
    {
        Schema::table('media', function ($table) {
            $table->dropColumn('generated_conversions');
        });

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('le faltan columnas: generated_conversions')
            ->assertFailed();
    }

    public function test_the_media_probe_row_does_not_survive(): void
    {
        $antes = DB::table('media')->count();

        $this->artisan('almacenamiento:verificar');

        $this->assertSame(
            $antes,
            DB::table('media')->count(),
            'La fila de prueba se quedó en la base: el ROLLBACK no está haciendo su trabajo.',
        );
    }

    public function test_the_check_refuses_to_pass_when_php_accepts_less_than_the_panel_promises(): void
    {
        // El panel anuncia PDF de 20 MB; PHP viene de fábrica con 2 MB. En un
        // servidor recién instalado nadie toca eso, y el archivo se descarta
        // antes de que Laravel lo vea. Dar «todo correcto» aquí sería repetir
        // el mismo error que este comando ya cometió una vez.
        config()->set('media.max_pdf_kb', $this->limiteDePhpEnKb() * 4);

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('PHP admite MENOS de lo que el panel promete')
            ->assertFailed();
    }

    public function test_the_check_passes_the_limits_when_php_is_generous_enough(): void
    {
        config()->set('media.max_image_kb', 1);
        config()->set('media.max_pdf_kb', 1);

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('PHP admite todo lo que el panel promete.');
    }

    private function limiteDePhpEnKb(): int
    {
        $valor = trim((string) ini_get('upload_max_filesize'));
        $numero = (int) $valor;

        return match (strtolower(substr($valor, -1))) {
            'g' => $numero * 1024 * 1024,
            'm' => $numero * 1024,
            'k' => $numero,
            default => max(1, intdiv($numero, 1024)),
        };
    }
}
