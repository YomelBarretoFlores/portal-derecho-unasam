<?php

namespace App\Console\Commands;

use App\Services\RevistaSubmissionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
}
