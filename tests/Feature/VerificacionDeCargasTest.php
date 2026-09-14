<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
