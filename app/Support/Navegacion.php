<?php

namespace App\Support;

/**
 * Arquitectura de información del sitio público, en un solo sitio.
 *
 * Vivía repartida: los grupos escritos a mano en la plantilla del menú, otro
 * reparto distinto en el pie, y una tercera nomenclatura —«Programa»,
 * «Académico», «Institucional», «Transparencia»— en las migas de pan de cada
 * página. Al reagrupar el menú por audiencia, las migas quedaron contradiciendo
 * al menú: se llegaba por «Estudiantes» a una página que decía «Académico».
 *
 * Aquí se guardan nombres de ruta, no URLs, para poder resolver el camino
 * inverso: dada la ruta actual, a qué grupo pertenece.
 */
class Navegacion
{
    /** @return array<string, array<int, array{0: string, 1: string, 2?: mixed}>> */
    public static function grupos(): array
    {
        return [
            'La Facultad' => [
                ['Presentación', 'presentacion'],
                ['Resumen', 'resumen'],
                ['Historia', 'historia'],
                ['Misión y Visión', 'mision'],
                ['Objetivos', 'objetivos'],
                ['Organigrama', 'organigrama'],
                ['Plana docente', 'docentes'],
            ],
            'Estudiantes' => [
                ['Plan de Estudios 2023', 'plan-2023'],
                ['Plan de Estudios 2019', 'plan-2019'],
                ['Competencias', 'competencias'],
                ['Perfil de Ingreso', 'perfil-ingreso'],
                ['Perfil de Egreso', 'perfil-egreso'],
                ['Campo Laboral', 'campo-laboral'],
                ['Documentos', 'documentos'],
            ],
            'Investigación' => [
                ['Revista Derecho y Cultura', 'revista'],
                ['Blog', 'blog'],
                ['Estadísticas', 'estadisticas', 'matriculados'],
            ],
        ];
    }

    /** Enlaces ya resueltos a URL, como los consumen el menú y el pie.
     *
     * @return array<string, array<int, array{0: string, 1: string}>>
     */
    public static function enlaces(): array
    {
        return collect(static::grupos())
            ->map(fn (array $items): array => collect($items)
                ->map(fn (array $item): array => [$item[0], route($item[1], $item[2] ?? [])])
                ->all())
            ->all();
    }

    /**
     * Grupo al que pertenece una ruta, para que la miga de pan diga lo mismo que
     * el menú por el que se llegó.
     *
     * La revista es un caso aparte: es una publicación con sus propias quince
     * secciones y su propia barra, así que todas ellas se identifican como
     * «Revista» y no como el grupo que las contiene.
     */
    public static function grupoDe(?string $rutaActual): ?string
    {
        if ($rutaActual === null) {
            return null;
        }

        if (str_starts_with($rutaActual, 'revista')) {
            return 'Revista';
        }

        foreach (static::grupos() as $grupo => $items) {
            foreach ($items as $item) {
                if ($item[1] === $rutaActual) {
                    return $grupo;
                }
            }
        }

        return match ($rutaActual) {
            'comunicados', 'comunicados.show' => 'Comunicados',
            'blog.show' => 'Investigación',
            'docentes.show' => 'La Facultad',
            default => null,
        };
    }
}
