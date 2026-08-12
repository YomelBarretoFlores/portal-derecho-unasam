<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use ZipArchive;

class BackupContent extends Command
{
    protected $signature = 'content:backup {--path= : Ruta del archivo ZIP de respaldo}';

    protected $description = 'Respalda todas las tablas de contenido y los archivos públicos locales';

    public function handle(): int
    {
        $path = $this->option('path') ?: storage_path('app/private/backups/content-'.now()->format('Ymd-His').'.zip');
        File::ensureDirectoryExists(dirname($path));

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("No se pudo crear el respaldo en {$path}.");
        }

        $tables = collect(Schema::getTables())->pluck('name')->filter()->values();
        $metadata = [
            'created_at' => now()->toIso8601String(),
            'connection' => config('database.default'),
            'database' => DB::connection()->getDatabaseName(),
            'tables' => [],
        ];

        foreach ($tables as $table) {
            $rows = DB::table($table)->get()->map(fn (object $row): array => (array) $row)->all();
            $metadata['tables'][$table] = count($rows);
            $zip->addFromString(
                "database/{$table}.json",
                json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            );
        }

        $mediaRoot = storage_path('app/public');
        if (File::isDirectory($mediaRoot)) {
            foreach (File::allFiles($mediaRoot) as $file) {
                $zip->addFile($file->getPathname(), 'storage/app/public/'.$file->getRelativePathname());
            }
        }

        $zip->addFromString('manifest.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $zip->addFromString('RESTORE.md', "Este respaldo contiene un JSON por tabla y una copia de storage/app/public.\nRestaure primero el esquema con las migraciones y luego importe las filas respetando las claves foráneas.\n");
        $zip->close();

        $this->info("Respaldo creado: {$path}");
        $this->table(['Tabla', 'Filas'], collect($metadata['tables'])->map(fn (int $count, string $table): array => [$table, $count])->values()->all());

        return self::SUCCESS;
    }
}
