<?php

namespace App\Services;

use App\Filament\Resources\RevistaEnvios\RevistaEnvioResource;
use App\Models\Revista;
use App\Models\RevistaEnvio;
use App\Models\RevistaEnvioVersion;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class RevistaSubmissionService
{
    public function available(): bool
    {
        return $this->unavailableReasons() === [];
    }

    /**
     * Motivos por los que la recepción pública está cerrada, en lenguaje llano.
     * Devuelve un array vacío cuando está abierta. Permite que el panel y la
     * consola expliquen *qué falta* en lugar de mostrar solo «cerrada».
     *
     * @return array<int, string>
     */
    public function unavailableReasons(): array
    {
        $reasons = [];

        if (! config('submissions.enabled')) {
            $reasons[] = 'SUBMISSIONS_ENABLED está en false.';
        }

        if (! config('submissions.privacy_approved')) {
            $reasons[] = 'SUBMISSIONS_PRIVACY_APPROVED está en false: falta aprobar la declaración de privacidad.';
        }

        if (! config('submissions.storage_persistent')) {
            $reasons[] = 'SUBMISSIONS_STORAGE_PERSISTENT está en false: no se ha declarado almacenamiento persistente.';
        }

        if (! $this->usesPrivateDisk()) {
            $reasons[] = sprintf('El disco «%s» no es privado: los manuscritos quedarían accesibles desde la web.', (string) config('submissions.disk'));
        }

        if (($faltante = $this->missingDiskSetting()) !== null) {
            $reasons[] = $faltante;
        }

        return $reasons;
    }

    /**
     * Ajuste imprescindible que le falta al disco de manuscritos, si alguno.
     *
     * Sin esto, un disco de proveedor a medio configurar pasaba todas las
     * comprobaciones: «privado» es cierto, pero el bucket estaba vacío. La
     * recepción se declaraba abierta y el primer manuscrito que llegara moría
     * al guardarse, después de que el autor hubiera rellenado el formulario y
     * subido su original.
     */
    public function missingDiskSetting(): ?string
    {
        $disk = (string) config('submissions.disk');
        $diskConfig = config("filesystems.disks.{$disk}");

        if (! is_array($diskConfig)) {
            return sprintf('El disco «%s» no está definido en config/filesystems.php.', $disk);
        }

        $requeridos = match ((string) ($diskConfig['driver'] ?? '')) {
            's3' => ['bucket' => 'AWS_SUBMISSIONS_BUCKET', 'key' => 'AWS_ACCESS_KEY_ID', 'secret' => 'AWS_SECRET_ACCESS_KEY'],
            'local' => ['root' => 'la raíz del disco'],
            default => [],
        };

        foreach ($requeridos as $clave => $variable) {
            if (blank($diskConfig[$clave] ?? null)) {
                return sprintf('Al disco «%s» le falta %s.', $disk, $variable);
            }
        }

        return null;
    }

    /**
     * Comprueba que el disco de manuscritos no sea alcanzable desde la web.
     */
    public function usesPrivateDisk(): bool
    {
        $disk = (string) config('submissions.disk');
        $diskConfig = config("filesystems.disks.{$disk}");

        if (! is_array($diskConfig) || $disk === 'public' || ($diskConfig['visibility'] ?? null) === 'public') {
            return false;
        }

        if (($diskConfig['driver'] ?? null) === 'local') {
            $root = str_replace('\\', '/', (string) ($diskConfig['root'] ?? ''));
            $publicRoot = rtrim(str_replace('\\', '/', public_path()), '/').'/';

            return $root !== '' && ! str_starts_with(rtrim($root, '/').'/', $publicRoot);
        }

        return true;
    }

    /** @param array<string, mixed> $data @param array<string, UploadedFile> $files */
    public function create(Revista $revista, array $data, array $files, ?string $ip): RevistaEnvio
    {
        $disk = (string) config('submissions.disk');
        $codigo = $this->trackingCode();
        $directory = 'revista-envios/'.strtolower($codigo);
        $stored = [];

        try {
            foreach ($files as $field => $file) {
                $stored[$field] = $this->storeFile(
                    $file,
                    $directory,
                    $field.'-'.Str::uuid().'.'.strtolower($file->getClientOriginalExtension()),
                    $disk,
                );
            }

            $envio = DB::transaction(fn (): RevistaEnvio => RevistaEnvio::query()->create([
                ...$data,
                'revista_id' => $revista->id,
                'codigo_seguimiento' => $codigo,
                'manuscrito_path' => $stored['manuscrito'],
                'carta_path' => $stored['carta'],
                'declaracion_path' => $stored['declaracion'],
                'constancia_estilo_path' => $stored['constancia_estilo'],
                'estado' => 'recibido',
                'consentimiento_at' => now(),
                'ip_hash' => $ip ? hash_hmac('sha256', $ip, (string) config('app.key')) : null,
            ]));

            $this->notifyEditorialTeam($envio, false);

            return $envio;
        } catch (\Throwable $exception) {
            foreach ($stored as $path) {
                Storage::disk($disk)->delete($path);
            }
            throw $exception;
        }
    }

    public function addCorrection(RevistaEnvio $envio, UploadedFile $file, ?string $note): RevistaEnvioVersion
    {
        $disk = (string) config('submissions.disk');
        $path = $this->storeFile(
            $file,
            'revista-envios/'.strtolower($envio->codigo_seguimiento).'/correcciones',
            'manuscrito-'.Str::uuid().'.'.strtolower($file->getClientOriginalExtension()),
            $disk,
        );

        try {
            $version = DB::transaction(function () use ($envio, $path, $note): RevistaEnvioVersion {
                $locked = RevistaEnvio::query()->lockForUpdate()->findOrFail($envio->id);
                $numero = ((int) $locked->versiones()->max('numero')) + 1;
                $version = $locked->versiones()->create(['numero' => $numero, 'archivo_path' => $path, 'nota_autor' => $note, 'recibida_at' => now()]);
                $locked->update(['estado' => 'correccion_recibida']);

                return $version;
            });

            $this->notifyEditorialTeam($envio->refresh(), true);

            return $version;
        } catch (\Throwable $exception) {
            Storage::disk($disk)->delete($path);
            throw $exception;
        }
    }

    private function trackingCode(): string
    {
        do {
            $code = 'DYC-'.Str::upper(Str::random(12));
        } while (RevistaEnvio::query()->where('codigo_seguimiento', $code)->exists());

        return $code;
    }

    private function storeFile(UploadedFile $file, string $directory, string $name, string $disk): string
    {
        $path = $file->storeAs($directory, $name, $disk);

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('No fue posible almacenar el archivo del envío.');
        }

        return $path;
    }

    private function notifyEditorialTeam(RevistaEnvio $envio, bool $isCorrection): void
    {
        try {
            // Solo estos dos roles pasan ContentPolicy::view, así que notificar a
            // cualquier otro produciría avisos que su destinatario no puede abrir.
            $recipients = User::query()
                ->whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_EDITOR])
                ->get();

            if ($recipients->isEmpty()) {
                return;
            }

            Notification::make()
                ->title($isCorrection ? 'Nueva corrección recibida' : 'Nuevo manuscrito recibido')
                ->body($envio->codigo_seguimiento.' · '.$envio->titulo)
                ->icon($isCorrection ? 'heroicon-o-document-arrow-up' : 'heroicon-o-inbox-arrow-down')
                ->iconColor('primary')
                ->actions([
                    Action::make('gestionar')
                        ->label('Gestionar envío')
                        ->url(RevistaEnvioResource::getUrl('edit', ['record' => $envio]))
                        ->markAsRead(),
                ])
                ->sendToDatabase($recipients);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
