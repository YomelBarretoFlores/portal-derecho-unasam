<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Documento;
use App\Models\RevistaNumero;
use App\Support\Navegacion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Buscador del sitio público.
 *
 * La búsqueda se resuelve en PHP sobre un índice cacheado, no con SQL. Tres
 * razones, en orden de peso:
 *
 * 1. Insensibilidad a tildes. «indexacion» tiene que encontrar «Indexación».
 *    En PostgreSQL eso exige la extensión unaccent, que no está garantizada en
 *    el alojamiento —lo despliega el área ETI y aún no sabemos con qué— y en
 *    SQLite, donde corren los tests, directamente no existe. Plegar los acentos
 *    en PHP se comporta igual en ambos.
 * 2. Las páginas estáticas —presentación, historia, plan de estudios— son
 *    buena parte del sitio y no viven en ninguna tabla.
 * 3. El volumen lo permite con holgura: el portal maneja decenas de registros,
 *    no miles.
 *
 * El límite es real y conviene dejarlo escrito: si la revista llega a publicar
 * miles de artículos, esto habrá que sustituirlo por búsqueda de texto completo
 * de PostgreSQL. El punto de corte razonable está en el orden del millar.
 */
class Buscador
{
    private const TTL = 3600;

    /** @return Collection<int, object> */
    public function buscar(string $consulta, int $limite = 40): Collection
    {
        $termino = $this->normalizar($consulta);

        if (mb_strlen($termino) < 2) {
            return collect();
        }

        $palabras = collect(preg_split('/\s+/', $termino))->filter()->values();

        return $this->indice()
            ->map(function (array $fila) use ($palabras): ?array {
                $puntos = 0;

                foreach ($palabras as $palabra) {
                    $enTitulo = str_contains($fila['_titulo'], $palabra);
                    $enCuerpo = str_contains($fila['_cuerpo'], $palabra);

                    if (! $enTitulo && ! $enCuerpo) {
                        return null; // Todas las palabras deben aparecer.
                    }

                    $puntos += $enTitulo ? 10 : 1;
                }

                return [...$fila, '_puntos' => $puntos];
            })
            ->filter()
            ->sortByDesc('_puntos')
            ->take($limite)
            ->map(fn (array $fila): object => (object) [
                'tipo' => $fila['tipo'],
                'titulo' => $fila['titulo'],
                'extracto' => $fila['extracto'],
                'url' => $fila['url'],
            ])
            ->values();
    }

    /**
     * Quita acentos y pasa a minúsculas, para que la búsqueda no dependa de
     * cómo se teclee. Sin esto, «MISION» y «misión» serían consultas distintas.
     */
    private function normalizar(string $texto): string
    {
        $plano = strtr(mb_strtolower(trim($texto)), [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
        ]);

        return preg_replace('/\s+/', ' ', $plano) ?? $plano;
    }

    /** @return Collection<int, array<string, mixed>> */
    private function indice(): Collection
    {
        $version = (int) Cache::get('content.version', 1);

        $filas = Cache::remember("buscador:indice:v{$version}", self::TTL, fn (): array => [
            ...$this->paginas(),
            ...$this->contenido(),
        ]);

        return collect($filas)->map(fn (array $fila): array => [
            ...$fila,
            '_titulo' => $this->normalizar($fila['titulo']),
            '_cuerpo' => $this->normalizar($fila['titulo'].' '.$fila['extracto']),
        ]);
    }

    /**
     * Páginas estáticas del portal y secciones de la revista. No viven en
     * ninguna tabla, pero son lo que más se busca en un sitio institucional.
     *
     * @return array<int, array<string, string>>
     */
    private function paginas(): array
    {
        $filas = [];

        foreach (Navegacion::grupos() as $grupo => $items) {
            foreach ($items as $item) {
                $filas[] = [
                    'tipo' => $grupo,
                    'titulo' => $item[0],
                    'extracto' => 'Página del portal · '.$grupo,
                    'url' => route($item[1], $item[2] ?? [], absolute: false),
                ];
            }
        }

        $revista = [
            ['revista.actual', 'Número actual'], ['revista.archivos', 'Archivos'],
            ['revista.politicas', 'Políticas editoriales'], ['revista.comite-editorial', 'Comité editorial'],
            ['revista.comite-cientifico', 'Comité científico'], ['revista.avisos', 'Avisos'],
            ['revista.envios', 'Envíos de manuscritos'], ['revista.normas', 'Normas para autores'],
            ['revista.formatos', 'Formatos y plantillas'], ['revista.sobre', 'Sobre la revista'],
            ['revista.indexacion', 'Indexación'], ['revista.contacto', 'Contacto de la revista'],
            ['revista.privacidad', 'Declaración de privacidad'], ['revista.preservacion', 'Preservación digital'],
            ['revista.envios.consulta', 'Consultar mi envío'],
        ];

        foreach ($revista as [$ruta, $titulo]) {
            $filas[] = [
                'tipo' => 'Revista',
                'titulo' => $titulo,
                'extracto' => 'Sección de Derecho y Cultura',
                'url' => route($ruta, absolute: false),
            ];
        }

        return $filas;
    }

    /** @return array<int, array<string, string>> */
    private function contenido(): array
    {
        $filas = [];

        foreach (Comunicado::query()->publicados()->latest('fecha_publicacion')->get() as $c) {
            $filas[] = [
                'tipo' => 'Comunicado', 'titulo' => $c->titulo,
                'extracto' => $this->resumir($c->resumen ?: $c->contenido),
                'url' => route('comunicados.show', $c->slug, absolute: false),
            ];
        }

        foreach (BlogPost::query()->publicados()->latest('fecha')->get() as $p) {
            $filas[] = [
                'tipo' => filled($p->tipo) ? Str::ucfirst((string) $p->tipo) : 'Blog',
                'titulo' => $p->titulo,
                'extracto' => $this->resumir($p->extracto ?: $p->contenido),
                'url' => route('blog.show', $p->slug, absolute: false),
            ];
        }

        foreach (Docente::query()->activos()->get() as $d) {
            $filas[] = [
                'tipo' => 'Docente', 'titulo' => $d->name,
                'extracto' => trim(implode(' · ', array_filter([$d->grado, $d->area]))),
                'url' => route('docentes.show', $d->slug, absolute: false),
            ];
        }

        foreach (Documento::query()->orderBy('orden')->get() as $doc) {
            $filas[] = [
                'tipo' => 'Documento', 'titulo' => $doc->titulo,
                'extracto' => (string) $doc->categoria,
                'url' => route('documentos', absolute: false),
            ];
        }

        foreach (RevistaNumero::query()->publicados()->get() as $n) {
            $filas[] = [
                'tipo' => 'Número', 'titulo' => $n->titulo,
                'extracto' => 'Vol. '.$n->volumen.' · Núm. '.$n->numero,
                'url' => route('revista.numero', $n->slug, absolute: false),
            ];
        }

        foreach (Articulo::query()->publicados()->with('numero')->get() as $a) {
            if ($a->numero === null) {
                continue;
            }

            $filas[] = [
                'tipo' => 'Artículo', 'titulo' => $a->titulo,
                'extracto' => trim(implode(' · ', $a->autores ?? []).' — '.$this->resumir($a->resumen), ' —'),
                'url' => route('revista.articulo', [$a->numero->slug, $a->slug], absolute: false),
            ];
        }

        return $filas;
    }

    private function resumir(?string $html): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $html)) ?? ''), 180);
    }
}
