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
        return config('submissions.enabled')
            && config('submissions.privacy_approved')
            && config('submissions.storage_persistent')
            && $this->usesPrivateDisk();
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

    private function usesPrivateDisk(): bool
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
            $recipients = User::query()
                ->where(function ($query): void {
                    $query->whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_EDITOR])
                        ->orWhere(function ($legacy): void {
                            $legacy->whereNull('role')->where('is_admin', true);
                        });
                })
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
