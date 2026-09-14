<?php

namespace App\Console\Commands;

use App\Services\RevistaSubmissionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;

/**
 * Comprueba que los dos almacenamientos del portal estén bien configurados
 * antes de habilitar cargas y recepción de manuscritos en producción.
 *
 * El portal usa DOS discos con requisitos opuestos:
 *   - medios públicos  → deben ser servibles por web
 *   - manuscritos      → nunca deben ser alcanzables por web
 */
class VerificarAlmacenamiento extends Command
{
    protected $signature = 'almacenamiento:verificar';

    protected $description = 'Verifica los discos de medios públicos y de manuscritos antes de habilitarlos en producción';

    public function handle(RevistaSubmissionService $submissions): int
    {
        $mediaDisk = (string) config('media-library.disk_name');
        $submissionsDisk = (string) config('submissions.disk');

        $this->info('Verificación de almacenamiento del portal');
        $this->newLine();

        $this->line('<options=bold>Modo configurado</>');
        $this->line('  Medios      : '.$this->describirModo($mediaDisk));
        $this->line('  Manuscritos : '.$this->describirModo($submissionsDisk));
        $this->newLine();

        $ok = $this->verificarDisco(
            'Medios públicos',
            $mediaDisk,
            debeSerPublico: true,
            habilitado: (bool) config('media.uploads_enabled'),
            variableInterruptor: 'MEDIA_UPLOADS_ENABLED',
        );

        $this->newLine();

        $ok = $this->verificarDisco(
            'Manuscritos recibidos',
            $submissionsDisk,
            debeSerPublico: false,
            habilitado: (bool) config('submissions.enabled'),
            variableInterruptor: 'SUBMISSIONS_ENABLED',
        ) && $ok;

        $this->newLine();
        $ok = $this->verificarProcesadoDeImagenes() && $ok;

        $this->newLine();
        $ok = $this->verificarRegistroDeMedios() && $ok;

        $this->newLine();
        $ok = $this->verificarCarpetasIntermedias() && $ok;

        $this->newLine();
        $ok = $this->informarDelEntorno() && $ok;

        $this->newLine();
        $this->line('<options=bold>Recepción pública de manuscritos</>');

        $motivos = $submissions->unavailableReasons();
        if ($motivos === []) {
            $this->line('  <fg=green>✔</> Abierta.');
        } else {
            $this->line('  <fg=yellow>●</> Cerrada. Motivos:');
            foreach ($motivos as $motivo) {
                $this->line("      - {$motivo}");
            }
        }

        $this->newLine();
        $this->warn('Lo que este comando NO puede comprobar:');
        $this->line('  La PERSISTENCIA entre despliegues no es detectable desde dentro del contenedor.');
        $this->line('  Un disco efímero (contenedor sin volumen montado) supera todas las pruebas de');
        $this->line('  arriba y aun así pierde los archivos en el siguiente despliegue.');
        $this->line('  Confírmalo con quien administre la infraestructura antes de poner');
        $this->line('  SUBMISSIONS_STORAGE_PERSISTENT=true. Ver DEPLOY.md.');

        return $ok ? self::SUCCESS : self::FAILURE;
    }

    private function verificarDisco(string $titulo, string $disco, bool $debeSerPublico, bool $habilitado, string $variableInterruptor): bool
    {
        $this->line("<options=bold>{$titulo}</> (disco «{$disco}»)");

        $config = config("filesystems.disks.{$disco}");

        if (! is_array($config)) {
            $this->line("  <fg=red>✘</> El disco «{$disco}» no está definido en config/filesystems.php.");

            return false;
        }

        $driver = (string) ($config['driver'] ?? 'desconocido');
        $this->line("  Driver: {$driver}");

        $ok = true;

        // Un disco de proveedor a medio configurar revienta más abajo con un
        // error de tipos de PHP que no dice nada a quien administra el
        // servidor. Se comprueba antes y se nombra la variable que falta.
        if ($driver === 's3') {
            $variables = [
                'bucket' => $disco === 'manuscritos' ? 'AWS_SUBMISSIONS_BUCKET' : 'AWS_BUCKET',
                'key' => 'AWS_ACCESS_KEY_ID',
                'secret' => 'AWS_SECRET_ACCESS_KEY',
            ];

            if ($debeSerPublico) {
                $variables['url'] = 'AWS_URL (además de los enlaces, alimenta la cabecera CSP)';
            }

            $faltantes = [];
            foreach ($variables as $clave => $variable) {
                if (blank($config[$clave] ?? null)) {
                    $faltantes[] = $variable;
                }
            }

            if ($faltantes !== []) {
                foreach ($faltantes as $variable) {
                    $this->line("  <fg=red>✘</> Falta {$variable}.");
                }

                $this->line('  <fg=yellow>●</> No se prueba la escritura: el disco está incompleto.');
                $this->line($habilitado
                    ? "  <fg=red>✘</> {$variableInterruptor}=true con el disco sin configurar."
                    : "  <fg=yellow>●</> {$variableInterruptor}=false (deshabilitado en este entorno)");

                return false;
            }
        }

        if ($driver === 'local') {
            $root = (string) ($config['root'] ?? '');
            $this->line("  Raíz:   {$root}");
            $this->line($this->esRutaWeb($root)
                ? '  <fg=yellow>●</> La raíz está dentro de public/: es alcanzable directamente por web.'
                : '  <fg=green>✔</> La raíz está fuera de public/.');

            // El disco de medios se sirve por el enlace simbólico public/storage.
            // Sin él, los archivos existen pero devuelven 404 al visitante.
            if ($debeSerPublico && ! $this->esRutaWeb($root)) {
                $enlace = public_path('storage');
                if (is_link($enlace) || is_dir($enlace)) {
                    $this->line('  <fg=green>✔</> Enlace public/storage presente.');
                } else {
                    $this->line('  <fg=red>✘</> Falta el enlace public/storage: ejecuta «php artisan storage:link».');
                    $ok = false;
                }
            }
        }

        // Privacidad: la exigencia es opuesta según el uso.
        $esPublico = $disco === 'public' || ($config['visibility'] ?? null) === 'public'
            || ($driver === 'local' && $this->esRutaWeb((string) ($config['root'] ?? '')));

        if ($debeSerPublico && ! $esPublico) {
            $this->line('  <fg=yellow>●</> Se esperaba un disco servible por web; este no lo es.');
        } elseif (! $debeSerPublico && $esPublico) {
            $this->line('  <fg=red>✘</> Este disco es público. Los manuscritos NO deben ser descargables sin autenticación.');
            $ok = false;
        } else {
            $this->line($debeSerPublico ? '  <fg=green>✔</> Servible por web, como corresponde.' : '  <fg=green>✔</> Privado, como corresponde.');
        }

        // Escritura real: escribir, leer y borrar.
        $ruta = '_verificacion/'.Str::uuid().'.txt';
        $contenido = 'verificacion-'.now()->toIso8601String();

        try {
            Storage::disk($disco)->put($ruta, $contenido);
            $leido = Storage::disk($disco)->get($ruta);
            Storage::disk($disco)->delete($ruta);

            if ($leido !== $contenido) {
                $this->line('  <fg=red>✘</> Lo leído no coincide con lo escrito.');
                $ok = false;
            } else {
                $this->line('  <fg=green>✔</> Escritura, lectura y borrado correctos.');
            }
        } catch (\Throwable $e) {
            $this->line('  <fg=red>✘</> No se pudo escribir: '.$e->getMessage());
            $ok = false;
        }

        $this->line($habilitado
            ? "  <fg=green>✔</> {$variableInterruptor}=true"
            : "  <fg=yellow>●</> {$variableInterruptor}=false (deshabilitado en este entorno)");

        return $ok;
    }

    /**
     * Traduce el nombre del disco a la infraestructura que representa.
     *
     * Quien administra el servidor no tiene por qué saber qué significa
     * «medios» o «local»; lo que necesita saber es si los archivos acabarán en
     * el disco de la máquina o en un bucket, porque son dos cosas que se
     * respaldan y se vigilan de maneras distintas.
     */
    private function describirModo(string $disco): string
    {
        $driver = (string) config("filesystems.disks.{$disco}.driver");

        return match ($driver) {
            'local' => "«{$disco}» — disco del propio servidor (necesita volumen persistente)",
            's3' => "«{$disco}» — proveedor compatible con S3 (bucket externo)",
            '' => "«{$disco}» — NO DEFINIDO en config/filesystems.php",
            default => "«{$disco}» — driver {$driver}",
        };
    }

    private function esRutaWeb(string $root): bool
    {
        if ($root === '') {
            return false;
        }

        $root = rtrim(str_replace('\\', '/', $root), '/').'/';
        $publicRoot = rtrim(str_replace('\\', '/', public_path()), '/').'/';

        return str_starts_with($root, $publicRoot);
    }

    /**
     * Comprueba que el portal pueda GENERAR una miniatura, no solo escribir.
     *
     * Esta parte faltaba, y era justo la que fallaba. El comando escribía un
     * fichero de texto en el disco, veía que se escribía y leía bien, y decía
     * que todo estaba correcto —mientras cada carga desde el panel devolvía un
     * error 500—. Escribir un .txt no toca la biblioteca de imágenes; subir un
     * retrato sí, porque de cada imagen se genera una versión reducida.
     *
     * Sin GD (o Imagick) esa generación revienta y el archivo no llega a
     * guardarse. El registro se crea igual, así que desde el panel parece que
     * funcionó y la imagen simplemente no está. Es el fallo más difícil de ver
     * de los que ha tenido este portal.
     */
    private function verificarProcesadoDeImagenes(): bool
    {
        $this->line('<options=bold>Procesado de imágenes</> (hace falta para cada foto que se suba)');

        $motor = match (true) {
            extension_loaded('imagick') => 'imagick',
            extension_loaded('gd') => 'gd',
            default => null,
        };

        if ($motor === null) {
            $this->line('  <fg=red>✘</> PHP no tiene ni GD ni Imagick.');
            $this->line('      Toda carga de imagen va a fallar con error 500, en docentes,');
            $this->line('      comunicados, blog y portadas de la revista.');
            $this->line('      Instálalas y reinicia PHP. Ejemplo en Debian/Ubuntu con PHP 8.4:');
            $this->line('      sudo apt install php8.4-gd php8.4-intl php8.4-zip php8.4-bcmath');

            return false;
        }

        $this->line("  <fg=green>✔</> Extensión disponible: {$motor}");

        if (config('media-library.image_driver') !== $motor && ! extension_loaded((string) config('media-library.image_driver'))) {
            $this->line(sprintf('  <fg=red>✘</> IMAGE_DRIVER está en «%s», que no está instalada. Póngala en «%s».',
                (string) config('media-library.image_driver'), $motor));

            return false;
        }

        // Generación real: se crea una imagen, se reduce y se comprueba que la
        // reducción tenga el tamaño pedido. Es la misma operación que hace la
        // biblioteca de medios al guardar cada carga.
        $origen = tempnam(sys_get_temp_dir(), 'verif').'.jpg';
        $destino = tempnam(sys_get_temp_dir(), 'verif').'.jpg';

        try {
            $lienzo = imagecreatetruecolor(120, 90);
            imagefilledrectangle($lienzo, 0, 0, 119, 89, imagecolorallocate($lienzo, 20, 40, 80));
            imagejpeg($lienzo, $origen);
            imagedestroy($lienzo);

            Image::load($origen)->width(40)->save($destino);

            [$ancho] = getimagesize($destino);

            if ($ancho !== 40) {
                $this->line("  <fg=red>✘</> La miniatura salió de {$ancho} px en vez de 40.");

                return false;
            }

            $this->line('  <fg=green>✔</> Miniatura generada correctamente.');

            return true;
        } catch (\Throwable $e) {
            $this->line('  <fg=red>✘</> No se pudo generar la miniatura: '.$e->getMessage());
            $this->line('      Esto es exactamente lo que rompe las cargas desde el panel.');

            return false;
        } finally {
            @unlink($origen);
            @unlink($destino);
        }
    }

    /**
     * Comprueba que la tabla «media» acepte una fila.
     *
     * Esta comprobación nació de un error 500 que solo aparecía en el servidor
     * de la UNASAM, al guardar cualquier registro con archivo adjunto —imagen o
     * PDF, daba igual—, mientras los registros de solo texto se guardaban bien.
     *
     * El primer diagnóstico fue «permisos en storage», y era falso: si el disco
     * no deja escribir, la biblioteca de medios se lo traga y devuelve false,
     * sin excepción y sin 500 (Filesystem::add captura DiskCannotBeAccessed).
     * Un 500 significa que reventó algo que NO está capturado, y lo primero de
     * esa lista es el INSERT en la tabla «media»: es el único paso del camino
     * que un registro de solo texto no recorre.
     *
     * La fila se inserta de verdad y se deshace con un ROLLBACK, así que no
     * queda nada en la base.
     */
    private function verificarRegistroDeMedios(): bool
    {
        $this->line('<options=bold>Tabla de archivos adjuntos</> (se escribe una fila por cada archivo)');

        try {
            if (! Schema::hasTable('media')) {
                $this->line('  <fg=red>✘</> La tabla «media» no existe en esta base de datos.');
                $this->line('      Toda carga de archivo va a dar error 500. Ejecuta «php artisan migrate --force».');

                return false;
            }
        } catch (\Throwable $e) {
            $this->line('  <fg=red>✘</> No se pudo consultar la base de datos: '.$e->getMessage());

            return false;
        }

        $requeridas = [
            'id', 'model_type', 'model_id', 'uuid', 'collection_name', 'name',
            'file_name', 'mime_type', 'disk', 'conversions_disk', 'size',
            'manipulations', 'custom_properties', 'generated_conversions',
            'responsive_images', 'order_column',
        ];

        $faltantes = array_values(array_filter(
            $requeridas,
            fn (string $columna) => ! Schema::hasColumn('media', $columna),
        ));

        if ($faltantes !== []) {
            $this->line('  <fg=red>✘</> A la tabla «media» le faltan columnas: '.implode(', ', $faltantes));
            $this->line('      La migración quedó a medias. Ejecuta «php artisan migrate --force».');

            return false;
        }

        $this->line('  <fg=green>✔</> La tabla «media» existe y tiene todas sus columnas.');

        // Insertar de verdad. Una tabla puede existir, tener las columnas
        // correctas y aun así rechazar el INSERT: permisos del usuario de base
        // de datos, una secuencia de id desincronizada, o el plan de consulta
        // que PostgreSQL guarda de antes de migrar.
        try {
            DB::beginTransaction();

            DB::table('media')->insert([
                'model_type' => 'verificacion',
                'model_id' => 0,
                'uuid' => (string) Str::uuid(),
                'collection_name' => 'verificacion',
                'name' => 'verificacion',
                'file_name' => 'verificacion.txt',
                'mime_type' => 'text/plain',
                'disk' => (string) config('media-library.disk_name'),
                'conversions_disk' => (string) config('media-library.disk_name'),
                'size' => 1,
                'manipulations' => '{}',
                'custom_properties' => '{}',
                'generated_conversions' => '{}',
                'responsive_images' => '{}',
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::rollBack();

            $this->line('  <fg=green>✔</> Acepta una fila nueva (la prueba se deshizo, no queda nada).');

            return true;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->line('  <fg=red>✘</> La tabla existe pero rechaza el INSERT:');
            $this->line('      '.$e->getMessage());
            $this->line('      ESTO es lo que devuelve el error 500 al subir un archivo desde el panel.');

            return false;
        }
    }

    /**
     * Las dos carpetas por las que pasa un archivo antes de llegar a su sitio.
     *
     * Ninguna de las dos está dentro de storage/app, que es la que todo el
     * mundo arregla cuando hay un problema de permisos:
     *
     *   - livewire-tmp   donde el navegador deja el archivo mientras el
     *                    formulario sigue abierto.
     *   - media-library/temp  donde se genera la miniatura antes de moverla.
     *
     * Van aparte porque fallan en momentos distintos y se parecen mucho desde
     * fuera: la primera rompe la carga antes de guardar, la segunda al guardar.
     */
    private function verificarCarpetasIntermedias(): bool
    {
        $this->line('<options=bold>Carpetas intermedias</> (por aquí pasa el archivo antes de su destino)');

        $ok = true;

        $temporalDeMiniaturas = config('media-library.temporary_directory_path') ?? storage_path('media-library/temp');
        $ok = $this->comprobarCarpetaEscribible('Miniaturas', (string) $temporalDeMiniaturas) && $ok;

        $discoTemporal = (string) (config('livewire.temporary_file_upload.disk') ?: config('filesystems.default'));
        $carpetaTemporal = (string) (config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp');
        $raiz = (string) config("filesystems.disks.{$discoTemporal}.root", '');

        if ($raiz !== '') {
            $ok = $this->comprobarCarpetaEscribible('Carga en curso', rtrim($raiz, '/').'/'.$carpetaTemporal) && $ok;
        } else {
            $this->line("  <fg=yellow>●</> Carga en curso: disco «{$discoTemporal}», no es una carpeta local.");
        }

        return $ok;
    }

    private function comprobarCarpetaEscribible(string $titulo, string $ruta): bool
    {
        if (! is_dir($ruta) && ! @mkdir($ruta, 0o775, true) && ! is_dir($ruta)) {
            $this->line("  <fg=red>✘</> {$titulo}: no existe y no se puede crear «{$ruta}».");

            return false;
        }

        $prueba = rtrim($ruta, '/').'/verificacion-'.Str::uuid().'.tmp';

        if (@file_put_contents($prueba, 'x') === false) {
            $this->line("  <fg=red>✘</> {$titulo}: existe pero no se puede escribir en «{$ruta}».");
            $this->line('      Dueño actual: '.$this->duenoDe($ruta));

            return false;
        }

        @unlink($prueba);
        $this->line("  <fg=green>✔</> {$titulo}: escribible ({$ruta})");

        return true;
    }

    /**
     * Con qué usuario del sistema se está ejecutando esto.
     *
     * Importa más de lo que parece: este comando suele lanzarse desde una
     * sesión de administración, y el panel corre con el usuario del servidor
     * web. Si no coinciden, el comando puede dar todo correcto y las cargas
     * seguir fallando. Para descartarlo, ejecútalo con el usuario del servidor
     * web, por ejemplo «sudo -u www-data php artisan almacenamiento:verificar».
     */
    private function informarDelEntorno(): bool
    {
        $this->line('<options=bold>Entorno</> (para comparar con el del servidor web)');

        $usuario = function_exists('posix_getpwuid') && function_exists('posix_geteuid')
            ? (posix_getpwuid(posix_geteuid())['name'] ?? '?')
            : get_current_user();

        $this->line("  Usuario del sistema : {$usuario}");
        $this->line('  PHP                 : '.PHP_VERSION);
        $this->line('  storage/            : '.$this->duenoDe(storage_path()));
        $this->line('  storage/app/public  : '.$this->duenoDe(storage_path('app/public')));
        $this->line('  Memoria disponible  : '.ini_get('memory_limit'));

        $this->newLine();
        $ok = $this->comprobarLimitesDeSubida();

        $this->newLine();
        $this->line('  <fg=yellow>●</> Si el panel sigue fallando con todo esto en verde, repite el');
        $this->line('      comando con el usuario del servidor web. Ejemplo:');
        $this->line('      sudo -u www-data php artisan almacenamiento:verificar');

        return $ok;
    }

    private function duenoDe(string $ruta): string
    {
        if (! file_exists($ruta)) {
            return 'no existe';
        }

        $usuario = function_exists('posix_getpwuid')
            ? (posix_getpwuid(fileowner($ruta))['name'] ?? (string) fileowner($ruta))
            : (string) fileowner($ruta);

        $grupo = function_exists('posix_getgrgid')
            ? (posix_getgrgid(filegroup($ruta))['name'] ?? (string) filegroup($ruta))
            : (string) filegroup($ruta);

        return sprintf('%s:%s %s', $usuario, $grupo, substr(sprintf('%o', fileperms($ruta)), -4));
    }

    /**
     * Contrasta lo que el portal promete con lo que PHP deja pasar.
     *
     * El panel dice que admite imágenes de 5 MB y PDF de 20 MB, pero PHP viene
     * de fábrica con 2 MB por archivo y 8 MB por envío. En un servidor recién
     * instalado, nadie toca eso. El resultado es que el formulario acepta el
     * archivo, la barra llega al final, y al guardar el envío llega vacío al
     * servidor porque PHP lo descartó entero antes de que Laravel lo viera.
     *
     * Se comprueba aquí porque es la clase de fallo que no deja rastro útil:
     * el registro no dice «archivo demasiado grande», dice que faltan campos
     * que el formulario sí mandó.
     */
    private function comprobarLimitesDeSubida(): bool
    {
        $porArchivo = $this->aBytes((string) ini_get('upload_max_filesize'));
        $porEnvio = $this->aBytes((string) ini_get('post_max_size'));

        $prometidoImagen = ((int) config('media.max_image_kb')) * 1024;
        $prometidoPdf = ((int) config('media.max_pdf_kb')) * 1024;
        $prometido = max($prometidoImagen, $prometidoPdf);

        $this->line('  Medido en                       : PHP '.PHP_SAPI.', '.(php_ini_loaded_file() ?: 'sin php.ini'));
        $this->line('  Tamaño máximo por archivo (PHP) : '.ini_get('upload_max_filesize'));
        $this->line('  Tamaño máximo del envío   (PHP) : '.ini_get('post_max_size'));
        $this->line('  Lo que el panel promete admitir : '.$this->enMegas($prometido)
            .' ('.$this->enMegas($prometidoImagen).' imagen, '.$this->enMegas($prometidoPdf).' PDF)');

        if ($porArchivo >= $prometido && $porEnvio > $prometido) {
            $this->line('  <fg=green>✔</> PHP admite todo lo que el panel promete.');

            return true;
        }

        $this->line('  <fg=red>✘</> PHP admite MENOS de lo que el panel promete.');
        $this->line('      Un archivo por encima del límite de PHP se descarta antes de que');
        $this->line('      Laravel lo vea: el formulario parece funcionar y al guardar falla.');
        $this->line('      En el php.ini del servidor (y reiniciar PHP-FPM):');
        $this->line('        upload_max_filesize = '.$this->enMegas($prometido, redondeoAlza: true));
        $this->line('        post_max_size = '.$this->enMegas($prometido * 2, redondeoAlza: true));
        $this->line('      Si hay nginx delante, además: client_max_body_size '
            .$this->enMegas($prometido * 2, redondeoAlza: true).';');

        $this->avisarDeQueEstoNoEsElPhpDelPanel();

        return false;
    }

    /**
     * Estos números son los del PHP de consola, y el panel no usa ese.
     *
     * En Debian y Ubuntu hay dos ficheros de configuración separados —uno en
     * cli/php.ini y otro en fpm/php.ini— y casi nunca dicen lo mismo, porque
     * los límites de subida no significan nada en la línea de comandos y ahí
     * nadie los toca. Así que este comando puede dar la alarma por un valor que
     * al panel no le afecta, o callarse con uno que sí.
     *
     * No se puede leer el otro fichero desde aquí con garantías, así que al
     * menos se dice en voz alta qué se ha medido y cómo mirar el que importa.
     */
    private function avisarDeQueEstoNoEsElPhpDelPanel(): void
    {
        if (PHP_SAPI !== 'cli') {
            return;
        }

        $this->newLine();
        $this->line('      <fg=yellow>●</> Ojo: estos dos números son los del PHP de consola.');
        $this->line('          El panel no usa ese, usa el de PHP-FPM, y en Debian y Ubuntu');
        $this->line('          son ficheros distintos. Comprueba el que de verdad importa:');
        $this->line('            php-fpm -i | grep -E "upload_max_filesize|post_max_size"');
        $this->line('          o, si eso no está a mano, busca los dos ficheros:');
        $this->line('            ls /etc/php/*/cli/php.ini /etc/php/*/fpm/php.ini');
        $this->line('          Hay que cambiarlo en el de fpm, y reiniciar: systemctl restart php*-fpm');
    }

    private function aBytes(string $valor): int
    {
        $valor = trim($valor);

        if ($valor === '' || $valor === '-1') {
            return PHP_INT_MAX;
        }

        $numero = (int) $valor;

        return match (strtolower(substr($valor, -1))) {
            'g' => $numero * 1024 * 1024 * 1024,
            'm' => $numero * 1024 * 1024,
            'k' => $numero * 1024,
            default => $numero,
        };
    }

    private function enMegas(int $bytes, bool $redondeoAlza = false): string
    {
        if ($bytes === PHP_INT_MAX) {
            return 'sin límite';
        }

        $megas = $bytes / 1024 / 1024;

        return ($redondeoAlza ? (int) ceil($megas) : round($megas, 1)).'M';
    }
}
