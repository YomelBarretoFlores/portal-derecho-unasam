<?php

namespace App\Services;

use App\Models\Acceso;
use App\Models\AreaLaboral;
use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Documento;
use App\Models\Estadistica;
use App\Models\Hito;
use App\Models\Organigrama;
use App\Models\PerfilIngresoArea;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PublicContentCache
{
    private const TTL = 3600;

    /** @return array<string, mixed> */
    public function home(): array
    {
        $data = $this->remember('home', function (): array {
            $revistaInstitucional = Revista::query()->publica()->with('media')->first();
            $numero = RevistaNumero::query()->publicados()->with('media')
                ->orderByDesc('fecha_publicacion')->orderByDesc('orden')->first();
            $articulos = $numero
                ? Articulo::query()->publicados()->where('revista_numero_id', $numero->id)
                    ->with('media')->orderBy('orden')->limit(4)->get()
                : collect();

            return [
                'posts' => BlogPost::query()->publicados()->with('media')->latest('fecha')->limit(4)->get()
                    ->map(fn (BlogPost $post): array => $this->blogArray($post))->all(),
                'articulos' => $articulos->map(fn (Articulo $articulo): array => $this->articuloArray($articulo, $numero?->slug))->all(),
                'numero' => $numero ? $this->numeroArray($numero) : null,
                'revista_institucional' => $revistaInstitucional ? [
                    'nombre' => $revistaInstitucional->nombre,
                    'nombre_corto' => $revistaInstitucional->nombre_corto,
                    'presentacion' => $revistaInstitucional->presentacion,
                    'unidad_responsable' => $revistaInstitucional->unidad_responsable,
                    'resolucion_numero' => $revistaInstitucional->resolucion_numero,
                    'resolucion_fecha' => $revistaInstitucional->resolucion_fecha?->toDateString(),
                    'periodicidad' => $revistaInstitucional->periodicidad,
                    'modalidad' => $revistaInstitucional->modalidad,
                    '_resolucion_url' => $revistaInstitucional->resolution_url,
                ] : null,
                'matriculados' => Estadistica::query()->where('tipo', 'matriculados')->orderBy('anio')->get(['anio', 'total'])->toArray(),
                'titulados' => Estadistica::query()->where('tipo', 'titulados')->latest('anio')->first(['anio', 'total'])?->toArray(),
                'accesos' => Acceso::query()->activos()->get()->toArray(),
            ];
        });

        return [
            'posts' => $this->objects($data['posts'], ['fecha']),
            'revista' => $this->objects($data['articulos']),
            'revistaNumero' => $data['numero'] ? $this->object($data['numero'], ['fecha_publicacion']) : null,
            'revistaInstitucional' => $data['revista_institucional']
                ? $this->object($data['revista_institucional'], ['resolucion_fecha'])
                : null,
            'matriculados' => $this->objects($data['matriculados']),
            'tituladosActual' => $data['titulados'] ? (object) $data['titulados'] : null,
            'accesos' => $this->objects($data['accesos']),
        ];
    }

    public function hitos(): Collection
    {
        return $this->objects($this->remember('hitos', fn (): array => Hito::query()
            ->orderBy('orden')->orderBy('anio')->get(['anio', 'titulo', 'descripcion'])->toArray()));
    }

    public function areasLaborales(): Collection
    {
        return $this->objects($this->remember('areas-laborales', fn (): array => AreaLaboral::query()
            ->orderBy('orden')->get(['titulo', 'descripcion'])->toArray()));
    }

    public function objetivos(): Collection
    {
        return collect($this->remember('objetivos', fn (): array => app(ObjetivoService::class)->planes()->all()));
    }

    public function competencias(): Collection
    {
        return collect($this->remember('competencias', fn (): array => app(CompetenciaService::class)->grupos()->all()));
    }

    public function cursos(string $plan): Collection
    {
        $items = $this->remember("cursos:{$plan}", fn (): array => Curso::query()->publicados($plan)->get()
            ->map(fn (Curso $curso): array => [
                'ciclo' => $curso->ciclo,
                'ciclo_romano' => $curso->ciclo_romano,
                'nombre' => $curso->nombre,
                'creditos' => $curso->creditos,
                'tipo' => $curso->tipo,
            ])->all());

        return $this->objects($items)->groupBy('ciclo');
    }

    public function perfilIngreso(): Collection
    {
        return $this->objects($this->remember('perfil-ingreso', fn (): array => PerfilIngresoArea::query()
            ->orderBy('orden')->get(['titulo', 'items'])->toArray()));
    }

    /** @return array<string, mixed> */
    public function organigrama(): array
    {
        return $this->remember('organigrama', function (): array {
            $organigrama = Organigrama::query()->with('media')->first();

            return [
                'titulo' => $organigrama?->titulo,
                'descripcion' => $organigrama?->descripcion,
                'imagen' => $organigrama?->imagen_url,
            ];
        });
    }

    /** @return array<string, Collection> */
    public function estadisticas(): array
    {
        $series = $this->remember('estadisticas', fn (): array => Estadistica::query()
            ->orderBy('tipo')->orderBy('anio')->get(['tipo', 'anio', 'total'])
            ->groupBy('tipo')->map->values()->map->toArray()->all());

        return collect(Estadistica::TIPOS)->mapWithKeys(fn (string $label, string $tipo): array => [
            $tipo => $this->objects($series[$tipo] ?? []),
        ])->all();
    }

    public function blog(?string $tipo, int $page = 1): LengthAwarePaginator
    {
        $tipo = in_array($tipo, ['noticia', 'opinion', 'evento'], true) ? $tipo : '';
        $items = $this->remember("blog:{$tipo}", fn (): array => BlogPost::query()->publicados()->with('media')
            ->when($tipo, fn ($query) => $query->where('tipo', $tipo))->latest('fecha')->get()
            ->map(fn (BlogPost $post): array => $this->blogArray($post))->all());

        return $this->paginate($items, 9, $page, ['fecha']);
    }

    public function comunicados(int $page = 1): LengthAwarePaginator
    {
        $items = $this->remember('comunicados', fn (): array => Comunicado::query()->publicados()->with('media')
            ->latest('fecha_publicacion')->get()->map(fn (Comunicado $comunicado): array => [
                'slug' => $comunicado->slug,
                'titulo' => $comunicado->titulo,
                'resumen' => $comunicado->resumen,
                'fecha_publicacion' => $comunicado->fecha_publicacion?->toIso8601String(),
                '_imagen_url' => $comunicado->getFirstMediaUrl('imagen', 'thumb'),
            ])->all());

        return $this->paginate($items, 9, $page, ['fecha_publicacion']);
    }

    public function docentes(int $page = 1): LengthAwarePaginator
    {
        $items = $this->remember('docentes', fn (): array => Docente::query()->activos()->with('media')->get()
            ->map(fn (Docente $docente): array => [
                'slug' => $docente->slug,
                'name' => $docente->name,
                'area' => $docente->area,
                'grado' => $docente->grado,
                'iniciales' => $docente->iniciales,
                '_foto_url' => $docente->fotoPublicaUrl('thumb'),
            ])->all());

        return $this->paginate($items, 12, $page);
    }

    /** @return array{revista: object|null, numeros: LengthAwarePaginator} */
    public function revista(?string $q, int $page = 1): array
    {
        $data = $this->remember('revista', function (): array {
            $revista = Revista::query()->publica()->with('media')->first();
            $numeros = RevistaNumero::query()->publicados()->with('media')
                ->orderByDesc('fecha_publicacion')->orderByDesc('orden')->get()
                ->map(fn (RevistaNumero $numero): array => [
                    ...$this->numeroArray($numero),
                    'descripcion' => $numero->descripcion,
                    '_portada_url' => $numero->getFirstMediaUrl('portada'),
                ])->all();

            return [
                'revista' => $revista ? [
                    'nombre' => $revista->nombre,
                    'nombre_corto' => $revista->nombre_corto,
                    'presentacion' => $revista->presentacion,
                    'enfoque_alcance' => $revista->enfoque_alcance,
                    'unidad_responsable' => $revista->unidad_responsable,
                    'resolucion_numero' => $revista->resolucion_numero,
                    'resolucion_fecha' => $revista->resolucion_fecha?->toDateString(),
                    'periodicidad' => $revista->periodicidad,
                    'modalidad' => $revista->modalidad,
                    'idiomas' => $revista->idiomas,
                    'sistema_arbitraje' => $revista->sistema_arbitraje,
                    'issn' => $revista->issn,
                    'contacto_email' => $revista->contacto_email,
                    'normas_publicacion' => $revista->normas_publicacion,
                    '_resolucion_url' => $revista->resolution_url,
                ] : null,
                'numeros' => $numeros,
            ];
        });
        $filtered = collect($data['numeros'])
            ->when($q, fn (Collection $rows) => $rows->filter(fn (array $row): bool => str_contains(mb_strtolower($row['titulo'].' '.($row['descripcion'] ?? '')), mb_strtolower((string) $q))))
            ->values()->all();

        return [
            'revista' => $data['revista'] ? $this->object($data['revista'], ['resolucion_fecha']) : null,
            'numeros' => $this->paginate($filtered, 12, $page, ['fecha_publicacion']),
        ];
    }

    /** @return array{documentos: LengthAwarePaginator, categorias: Collection} */
    public function documentos(?string $q, ?string $categoria, int $page = 1): array
    {
        $items = $this->remember('documentos', fn (): array => Documento::query()->with('media')
            ->orderBy('orden')->orderByDesc('fecha')->get()->map(fn (Documento $documento): array => [
                'titulo' => $documento->titulo,
                'categoria' => $documento->categoria,
                'fecha' => $documento->fecha?->toDateString(),
                'enlace' => $documento->enlace,
            ])->all());
        $categorias = collect($items)->pluck('categoria')->filter()->unique()->sort()->values();
        $filtered = collect($items)
            ->when($q, fn (Collection $rows) => $rows->filter(fn (array $row): bool => str_contains(mb_strtolower($row['titulo'].' '.$row['categoria']), mb_strtolower((string) $q))))
            ->when($categoria, fn (Collection $rows) => $rows->where('categoria', $categoria))->values()->all();

        return ['documentos' => $this->paginate($filtered, 20, $page, ['fecha']), 'categorias' => $categorias];
    }

    /** @return array<string, mixed> */
    public function warm(): array
    {
        $this->home();
        $this->hitos();
        $this->areasLaborales();
        $this->objetivos();
        $this->competencias();
        $this->cursos('2023');
        $this->cursos('2019');
        $this->perfilIngreso();
        $this->organigrama();
        $this->estadisticas();
        $this->blog(null);
        $this->comunicados();
        $this->docentes();
        $this->revista(null);
        $this->documentos(null, null);

        return ['version' => $this->version(), 'warmed_at' => now()->toIso8601String()];
    }

    /** @return array<string, mixed> */
    private function blogArray(BlogPost $post): array
    {
        return [
            'tipo' => $post->tipo,
            'titulo' => $post->titulo,
            'slug' => $post->slug,
            'extracto' => $post->extracto,
            'autor' => $post->autor,
            'tiempo_lectura' => $post->tiempo_lectura,
            'fecha' => $post->fecha?->toDateString(),
            '_imagen_url' => $post->getFirstMediaUrl('imagen', 'thumb'),
        ];
    }

    /** @return array<string, mixed> */
    private function articuloArray(Articulo $articulo, ?string $numeroSlug): array
    {
        return [
            'titulo' => $articulo->titulo,
            'slug' => $articulo->slug,
            'autores' => $articulo->autores ?? [],
            'paginas' => $articulo->paginas,
            'categoria' => $articulo->categoria,
            'resumen' => $articulo->resumen,
            'doi' => $articulo->doi,
            'descargas' => $articulo->descargas,
            '_pdf_url' => $articulo->getFirstMediaUrl('pdf'),
            'numero' => $numeroSlug ? (object) ['slug' => $numeroSlug] : null,
        ];
    }

    /** @return array<string, mixed> */
    private function numeroArray(RevistaNumero $numero): array
    {
        return [
            'slug' => $numero->slug,
            'titulo' => $numero->titulo,
            'volumen' => $numero->volumen,
            'numero' => $numero->numero,
            'fecha_publicacion' => $numero->fecha_publicacion?->toDateString(),
        ];
    }

    /** @template T @param callable(): T $resolver @return T */
    private function remember(string $key, callable $resolver): mixed
    {
        return Cache::remember("public:{$key}:v{$this->version()}", self::TTL, $resolver);
    }

    private function version(): int
    {
        return (int) Cache::get('content.version', 1);
    }

    /** @param array<int, array<string, mixed>> $items @param array<int, string> $dates */
    private function objects(array $items, array $dates = []): Collection
    {
        return collect($items)->map(fn (array $item): object => $this->object($item, $dates));
    }

    /** @param array<string, mixed> $item @param array<int, string> $dates */
    private function object(array $item, array $dates = []): object
    {
        foreach ($dates as $date) {
            $item[$date] = filled($item[$date] ?? null) ? Carbon::parse($item[$date]) : null;
        }

        return (object) $item;
    }

    /** @param array<int, array<string, mixed>> $items @param array<int, string> $dates */
    private function paginate(array $items, int $perPage, int $page, array $dates = []): LengthAwarePaginator
    {
        $collection = $this->objects($items, $dates);

        return new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()],
        );
    }
}
