<?php

namespace Tests\Feature;

use App\Services\RevistaSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El comando de verificación y los motivos de cierre son lo que ETI y el equipo
 * editorial usarán para diagnosticar el almacenamiento en producción.
 */
class AlmacenamientoTest extends TestCase
{
    use RefreshDatabase;

    private bool $enlaceCreadoPorElTest = false;

    protected function setUp(): void
    {
        parent::setUp();

        // El comando exige el enlace public/storage, que en producción crea
        // docker/entrypoint.sh pero no existe en un checkout limpio ni en CI.
        // El test crea su propia precondición en lugar de depender del entorno.
        if (! file_exists(public_path('storage'))) {
            @symlink(storage_path('app/public'), public_path('storage'));
            $this->enlaceCreadoPorElTest = is_link(public_path('storage'));
        }
    }

    protected function tearDown(): void
    {
        if ($this->enlaceCreadoPorElTest && is_link(public_path('storage'))) {
            @unlink(public_path('storage'));
        }

        parent::tearDown();
    }

    public function test_the_command_reports_both_disks_and_succeeds_when_they_are_correct(): void
    {
        config()->set('submissions.disk', 'local');

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('Medios públicos')
            ->expectsOutputToContain('Manuscritos recibidos')
            ->expectsOutputToContain('PERSISTENCIA')
            ->assertSuccessful();
    }

    public function test_the_command_fails_when_the_public_storage_link_is_missing(): void
    {
        // Sin el enlace, los medios se guardan pero el visitante recibe 404.
        // Es justo el paso que se olvida en un despliegue nuevo.
        if (! is_link(public_path('storage'))) {
            $this->markTestSkipped('public/storage no es un enlace simbólico en este entorno.');
        }

        $destino = readlink(public_path('storage'));
        unlink(public_path('storage'));

        try {
            $this->artisan('almacenamiento:verificar')
                ->expectsOutputToContain('Falta el enlace public/storage')
                ->assertFailed();
        } finally {
            symlink($destino, public_path('storage'));
        }
    }

    public function test_the_command_fails_when_manuscripts_would_sit_on_a_public_disk(): void
    {
        config()->set('submissions.disk', 'public');

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('NO deben ser descargables sin autenticación')
            ->assertFailed();
    }

    public function test_closed_reception_explains_which_switch_is_missing(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', false);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');

        $motivos = app(RevistaSubmissionService::class)->unavailableReasons();

        $this->assertCount(1, $motivos);
        $this->assertStringContainsString('SUBMISSIONS_PRIVACY_APPROVED', $motivos[0]);
    }

    public function test_a_public_disk_keeps_reception_closed_even_with_every_switch_on(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'public');

        $submissions = app(RevistaSubmissionService::class);

        $this->assertFalse($submissions->available());
        $this->assertStringContainsString('no es privado', implode(' ', $submissions->unavailableReasons()));
    }

    public function test_reception_is_open_when_everything_lines_up(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');

        $this->assertSame([], app(RevistaSubmissionService::class)->unavailableReasons());
        $this->assertTrue(app(RevistaSubmissionService::class)->available());
    }

    /**
     * El portal tiene que servir tanto si TI monta un disco propio como si
     * contrata un proveedor de objetos, sin tocar código. Estos tests fijan
     * ese contrato: los dos discos existen y cada uno cumple su exigencia.
     */
    public function test_both_storage_modes_are_configured(): void
    {
        $this->assertIsArray(
            config('filesystems.disks.public'),
            'Falta el disco «public»: es el modo de disco propio del servidor.',
        );

        $this->assertIsArray(
            config('filesystems.disks.medios'),
            'Falta el disco «medios»: es el modo de proveedor compatible con S3.',
        );

        $this->assertSame('s3', config('filesystems.disks.medios.driver'));
        $this->assertSame('public', config('filesystems.disks.medios.visibility'));
    }

    public function test_the_manuscript_provider_disk_is_private_and_has_no_public_url(): void
    {
        // Un manuscrito es un original inédito con datos personales del autor.
        // Si el disco tuviera «url», Media Library o cualquier vista podrían
        // generar un enlace directo al bucket, saltándose la autenticación.
        $this->assertSame('private', config('filesystems.disks.manuscritos.visibility'));
        $this->assertNull(config('filesystems.disks.manuscritos.url'));

        config()->set('submissions.disk', 'manuscritos');
        $this->assertTrue(app(RevistaSubmissionService::class)->usesPrivateDisk());
    }

    public function test_a_half_configured_provider_disk_keeps_reception_closed(): void
    {
        // Todos los interruptores en true y el disco «privado», pero sin bucket.
        // Antes esto se declaraba «Abierta»: el formulario se publicaba y el
        // primer manuscrito moría al guardarse, con el autor ya comprometido.
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'manuscritos');
        config()->set('filesystems.disks.manuscritos.bucket', null);

        $submissions = app(RevistaSubmissionService::class);

        $this->assertFalse($submissions->available());
        $this->assertStringContainsString('AWS_SUBMISSIONS_BUCKET', implode(' ', $submissions->unavailableReasons()));
    }

    public function test_a_fully_configured_provider_disk_opens_reception(): void
    {
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'manuscritos');
        config()->set('filesystems.disks.manuscritos.bucket', 'manuscritos-unasam');
        config()->set('filesystems.disks.manuscritos.key', 'clave');
        config()->set('filesystems.disks.manuscritos.secret', 'secreto');

        $this->assertSame([], app(RevistaSubmissionService::class)->unavailableReasons());
    }

    public function test_the_command_names_the_missing_variable_instead_of_dumping_a_php_error(): void
    {
        config()->set('submissions.disk', 'manuscritos');
        config()->set('filesystems.disks.manuscritos.bucket', null);

        $this->artisan('almacenamiento:verificar')
            ->expectsOutputToContain('Falta AWS_SUBMISSIONS_BUCKET')
            ->doesntExpectOutputToContain('AwsS3V3Adapter::__construct')
            ->assertFailed();
    }

    public function test_pointing_manuscripts_at_the_media_disk_is_refused(): void
    {
        // El error fácil de cometer al configurar un proveedor: un solo bucket
        // para todo. Deja los manuscritos al alcance de quien adivine la ruta.
        config()->set('submissions.disk', 'medios');

        $this->assertFalse(app(RevistaSubmissionService::class)->usesPrivateDisk());
    }

    /**
     * Tres capas distintas limitan una misma subida y tienen que decir lo
     * mismo: PHP, el campo del formulario y Media Library. Cuando no coinciden,
     * el archivo se transfiere entero y se rechaza al final, con un error que
     * no explica cuál de las tres lo rechazó.
     */
    public function test_the_three_upload_size_limits_agree(): void
    {
        $phpMaxKb = $this->limiteDePhpEnKb();
        $formularioMaxKb = max((int) config('media.max_pdf_kb'), (int) config('media.max_image_kb'));
        $mediaLibraryMaxKb = (int) (config('media-library.max_file_size') / 1024);

        $this->assertSame(
            $formularioMaxKb,
            $mediaLibraryMaxKb,
            'Media Library rechaza antes que el formulario: la subida muere después de transferirse.',
        );

        $this->assertGreaterThanOrEqual(
            $formularioMaxKb,
            $phpMaxKb,
            "PHP corta en {$phpMaxKb} KB pero el formulario admite {$formularioMaxKb} KB (docker/uploads.ini).",
        );
    }

    /**
     * config/media-library.php está publicado ENTERO a propósito.
     *
     * Con un fichero parcial, mergeConfigFrom() completaría el resto en local
     * pero no en producción: se salta la fusión cuando la configuración está
     * cacheada, y docker/entrypoint.sh ejecuta config:cache. Las claves que
     * faltaran quedarían nulas solo en el servidor.
     */
    public function test_the_published_media_library_config_is_complete(): void
    {
        // Se inspecciona el FICHERO, no config(). Leer la configuración en
        // ejecución no probaría nada: durante los tests no está cacheada, así
        // que mergeConfigFrom() rellena por detrás las claves que falten y el
        // test pasaría igual con el fichero mutilado. El fallo solo aparecería
        // en producción, que es donde no se puede depurar.
        $publicado = require config_path('media-library.php');
        $delPaquete = require base_path('vendor/spatie/laravel-medialibrary/config/media-library.php');

        $ausentes = array_diff(array_keys($delPaquete), array_keys($publicado));

        $this->assertSame(
            [],
            array_values($ausentes),
            'config/media-library.php ha perdido claves del paquete. Con config:cache quedarían nulas solo en producción.',
        );
    }

    /** Límite real de subida de PHP, en KB, leído de docker/uploads.ini. */
    private function limiteDePhpEnKb(): int
    {
        $ini = parse_ini_file(base_path('docker/uploads.ini')) ?: [];

        $aKb = static fn (string $valor): int => match (strtoupper(substr(trim($valor), -1))) {
            'G' => (int) $valor * 1024 * 1024,
            'M' => (int) $valor * 1024,
            'K' => (int) $valor,
            default => (int) ((int) $valor / 1024),
        };

        // post_max_size acota el cuerpo entero de la petición; upload_max_filesize,
        // cada archivo. Manda el menor de los dos.
        return min(
            $aKb((string) ($ini['upload_max_filesize'] ?? '0')),
            $aKb((string) ($ini['post_max_size'] ?? '0')),
        );
    }
}
