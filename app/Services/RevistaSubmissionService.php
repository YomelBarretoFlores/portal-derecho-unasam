<?php

namespace App\Services;

use App\Models\Revista;
use App\Models\RevistaEnvio;
use App\Models\RevistaEnvioVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RevistaSubmissionService
{
    public function available(): bool
    {
        return config('submissions.enabled')
            && config('submissions.privacy_approved')
            && config('submissions.storage_persistent');
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
                $stored[$field] = $file->storeAs($directory, $field.'-'.Str::uuid().'.'.strtolower($file->getClientOriginalExtension()), $disk);
            }

            return DB::transaction(fn (): RevistaEnvio => RevistaEnvio::query()->create([
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
        $path = $file->storeAs(
            'revista-envios/'.strtolower($envio->codigo_seguimiento).'/correcciones',
            'manuscrito-'.Str::uuid().'.'.strtolower($file->getClientOriginalExtension()),
            $disk,
        );

        try {
            return DB::transaction(function () use ($envio, $path, $note): RevistaEnvioVersion {
                $locked = RevistaEnvio::query()->lockForUpdate()->findOrFail($envio->id);
                $numero = ((int) $locked->versiones()->max('numero')) + 1;
                $version = $locked->versiones()->create(['numero' => $numero, 'archivo_path' => $path, 'nota_autor' => $note, 'recibida_at' => now()]);
                $locked->update(['estado' => 'correccion_recibida']);

                return $version;
            });
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
}
