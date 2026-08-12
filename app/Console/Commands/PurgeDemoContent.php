<?php

namespace App\Console\Commands;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Curso;
use App\Models\Docente;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\MediaLibrary\HasMedia;

class PurgeDemoContent extends Command
{
    protected $signature = 'content:purge-demo
        {--force : Elimina los registros encontrados. Sin esta opción solo inspecciona}
        {--manifest= : Ruta opcional del manifiesto JSON}';

    protected $description = 'Inspecciona o elimina únicamente el contenido demostrativo conocido';

    /** @var array<int, string> */
    private const BLOG_SLUGS = [
        'estudiantes-de-derecho-unasam-destacan-en-concurso-nacional-de-litigacion-oral',
        'el-rol-del-abogado-en-la-era-de-la-inteligencia-artificial',
        'seminario-internacional-de-derecho-constitucional-2026',
        'facultad-de-derecho-firma-convenio-con-el-poder-judicial-de-ancash',
        'justicia-ambiental-en-ancash-deudas-pendientes',
        'conferencia-reforma-procesal-penal-avances-y-retrocesos',
    ];

    /** @var array<int, string> */
    private const COMUNICADO_SLUGS = [
        'cronograma-de-matricula-2026-i',
        'convocatoria-a-practicas-preprofesionales',
        'suspension-de-actividades-academicas',
        'proceso-de-sustentacion-de-tesis',
    ];

    /** @var array<int, string> */
    private const DOCENTE_NAMES = [
        'Dr. Carlos Javier Rojas Meza',
        'Dra. María Elena Quispe Huamán',
        'Mg. Jorge Alberto Sánchez Díaz',
        'Dra. Ana Beatriz Villanueva Torres',
        'Mg. Roberto Mendoza Calderón',
        'Dr. Eduardo Vargas Linares',
        'Mg. Patricia Mendoza Ríos',
        'Dr. Diego Alejandro Huerta Castillo',
    ];

    /** @var array<int, string> */
    private const COURSE_NAMES = [
        'Introducción a las Ciencias Jurídicas',
        'Filosofía',
        'Comunicación',
        'Realidad Nacional y Regional',
        'Teoría General del Derecho',
        'Derecho Romano',
        'Metodología del Trabajo Universitario',
        'Economía Política',
        'Derecho Constitucional General',
        'Acto Jurídico',
        'Derecho Penal — Parte General',
    ];

    public function handle(): int
    {
        $groups = $this->queries();
        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'database' => config('database.default'),
            'mode' => $this->option('force') ? 'purge' : 'dry-run',
            'groups' => [],
            'total' => 0,
        ];

        foreach ($groups as $label => $query) {
            $records = is_a($query->getModel(), HasMedia::class)
                ? $query->with('media')->get()
                : $query->get();
            $manifest['groups'][$label] = $records->map(fn (Model $record): array => [
                'id' => $record->getKey(),
                'label' => $this->recordLabel($record),
                'media' => ($record instanceof HasMedia ? $record->media : collect())->map(fn ($media): array => [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'collection' => $media->collection_name,
                ])->all(),
            ])->all();
            $manifest['total'] += $records->count();
        }

        $this->renderSummary($manifest);

        if (! $this->option('force')) {
            $this->warn('Inspección terminada. No se eliminó ningún registro. Usa --force después de verificar el respaldo.');
            $this->writeManifest($manifest);

            return self::SUCCESS;
        }

        foreach ($groups as $query) {
            $query->get()->each->delete();
        }

        $remaining = collect($this->queries())->sum(fn (Builder $query): int => $query->count());
        if ($remaining !== 0) {
            throw new RuntimeException("La limpieza no terminó: quedan {$remaining} registros demostrativos conocidos.");
        }

        $manifest['completed_at'] = now()->toIso8601String();
        $manifest['remaining'] = 0;
        $path = $this->writeManifest($manifest);
        $this->info("Limpieza terminada. Se eliminaron {$manifest['total']} registros. Manifiesto: {$path}");

        return self::SUCCESS;
    }

    /** @return array<string, Builder> */
    private function queries(): array
    {
        return [
            'blog' => BlogPost::query()->whereIn('slug', self::BLOG_SLUGS),
            'articulos' => Articulo::query()->where('doi', 'like', '10.37249/rjunasam.v1i1.%'),
            'docentes' => Docente::query()->whereIn('name', self::DOCENTE_NAMES)->whereNull('documento_fuente'),
            'comunicados' => Comunicado::query()->whereIn('slug', self::COMUNICADO_SLUGS),
            'cursos' => Curso::query()->where('plan', '2023')->whereIn('nombre', self::COURSE_NAMES),
        ];
    }

    /** @param array<string, mixed> $manifest */
    private function renderSummary(array $manifest): void
    {
        $rows = [];
        foreach ($manifest['groups'] as $label => $records) {
            $rows[] = [$label, count($records), collect($records)->sum(fn (array $record): int => count($record['media']))];
        }

        $this->table(['Grupo', 'Registros', 'Medios'], $rows);
        $this->line("Total identificado: {$manifest['total']}");
    }

    /** @param array<string, mixed> $manifest */
    private function writeManifest(array $manifest): string
    {
        $path = $this->option('manifest') ?: storage_path('app/private/demo-purge-'.now()->format('Ymd-His').'-'.Str::random(6).'.json');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $path;
    }

    private function recordLabel(Model $record): string
    {
        return (string) ($record->getAttribute('titulo') ?? $record->getAttribute('name') ?? $record->getAttribute('nombre') ?? $record->getKey());
    }
}
