<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Datos de ejemplo para la fase de maquetación de la interfaz pública.
 *
 * IMPORTANTE: cada método devuelve objetos con la MISMA forma que tendrán los
 * futuros modelos Eloquent (mismos nombres de campo). Así, al conectar el CMS,
 * solo cambia la fuente (Service/Repository) y las vistas Blade NO se tocan.
 *
 * Contenido tomado de los prototipos de diseño (data.jsx).
 */
class MockData
{
    /**
     * Artículos de la Revista Jurídica.
     */
    public static function revistaArticulos(): Collection
    {
        return collect([
            [
                'id' => 1,
                'titulo' => 'Análisis del derecho a la consulta previa en comunidades campesinas de Áncash',
                'autores' => ['María Elena Quispe Huamán', 'Carlos Javier Rojas Meza'],
                'paginas' => '9-42',
                'categoria' => 'Derecho Constitucional',
                'resumen' => 'El presente artículo examina la aplicación del derecho a la consulta previa en las comunidades campesinas de la región Áncash, identificando las brechas entre el marco normativo vigente y su implementación efectiva en proyectos de inversión.',
                'fecha' => '2026-03-15',
                'doi' => '10.37249/rjunasam.v1i1.001',
                'descargas' => 342,
            ],
            [
                'id' => 2,
                'titulo' => 'La responsabilidad penal de las personas jurídicas en el ordenamiento peruano',
                'autores' => ['Luis Fernando Armendáriz Ochoa'],
                'paginas' => '43-78',
                'categoria' => 'Derecho Penal',
                'resumen' => 'Se analiza el régimen de responsabilidad penal aplicable a las personas jurídicas en el Perú, con especial atención a la Ley N° 30424 y sus modificatorias, evaluando su eficacia en la prevención de delitos corporativos.',
                'fecha' => '2026-03-15',
                'doi' => '10.37249/rjunasam.v1i1.002',
                'descargas' => 287,
            ],
            [
                'id' => 3,
                'titulo' => 'Derecho ambiental y minería: conflictos socioambientales en la región Áncash',
                'autores' => ['Ana Beatriz Villanueva Torres', 'Roberto Mendoza Calderón'],
                'paginas' => '79-118',
                'categoria' => 'Derecho Ambiental',
                'resumen' => 'Investigación sobre los conflictos socioambientales derivados de la actividad minera en Áncash, proponiendo mecanismos de resolución basados en el diálogo intercultural y el fortalecimiento institucional.',
                'fecha' => '2026-03-15',
                'doi' => '10.37249/rjunasam.v1i1.003',
                'descargas' => 456,
            ],
            [
                'id' => 4,
                'titulo' => 'La tutela jurisdiccional efectiva en el proceso contencioso administrativo',
                'autores' => ['Jorge Alberto Sánchez Díaz'],
                'paginas' => '119-152',
                'categoria' => 'Derecho Administrativo',
                'resumen' => 'Estudio sobre la efectividad del proceso contencioso administrativo como garantía del derecho a la tutela jurisdiccional efectiva, con análisis de la jurisprudencia del Tribunal Constitucional peruano.',
                'fecha' => '2026-03-15',
                'doi' => '10.37249/rjunasam.v1i1.004',
                'descargas' => 198,
            ],
            [
                'id' => 5,
                'titulo' => 'Reforma del sistema de justicia: perspectivas desde las regiones',
                'autores' => ['Patricia Mendoza Ríos', 'Eduardo Vargas Linares'],
                'paginas' => '153-190',
                'categoria' => 'Derecho Procesal',
                'resumen' => 'Se evalúan las reformas del sistema de justicia peruano desde una perspectiva descentralizada, identificando los desafíos específicos que enfrentan las regiones fuera de Lima para acceder a una justicia oportuna y de calidad.',
                'fecha' => '2026-03-15',
                'doi' => '10.37249/rjunasam.v1i1.005',
                'descargas' => 321,
            ],
            [
                'id' => 6,
                'titulo' => 'Inteligencia artificial y derecho: retos para la profesión jurídica en el Perú',
                'autores' => ['Diego Alejandro Huerta Castillo'],
                'paginas' => '191-224',
                'categoria' => 'Derecho y Tecnología',
                'resumen' => 'Análisis de los desafíos que la inteligencia artificial plantea al ejercicio profesional del derecho en el Perú, incluyendo implicancias éticas, regulatorias y de formación académica.',
                'fecha' => '2026-03-15',
                'doi' => '10.37249/rjunasam.v1i1.006',
                'descargas' => 512,
            ],
        ])->map(fn ($a) => (object) [...$a, 'fecha' => Carbon::parse($a['fecha'])]);
    }

    /**
     * Categorías para los filtros de la Revista.
     *
     * @return array<int, string>
     */
    public static function categorias(): array
    {
        return [
            'Derecho Constitucional', 'Derecho Penal', 'Derecho Ambiental',
            'Derecho Administrativo', 'Derecho Procesal', 'Derecho y Tecnología',
            'Derecho Civil', 'Derecho Laboral',
        ];
    }

    /**
     * Publicaciones del Blog.
     */
    public static function blogPosts(): Collection
    {
        return collect([
            ['id' => 1, 'tipo' => 'noticia', 'titulo' => 'Estudiantes de Derecho UNASAM destacan en concurso nacional de litigación oral', 'extracto' => 'Tres estudiantes del programa obtuvieron el segundo lugar en el XV Concurso Nacional de Litigación Oral organizado por el Poder Judicial.', 'fecha' => '2026-05-10', 'autor' => 'Redacción FDCCPP', 'tiempo_lectura' => '3 min'],
            ['id' => 2, 'tipo' => 'opinion', 'titulo' => 'El rol del abogado en la era de la inteligencia artificial', 'extracto' => 'Reflexión sobre cómo la IA está transformando la práctica jurídica y qué competencias deben desarrollar los futuros abogados para mantenerse relevantes.', 'fecha' => '2026-05-05', 'autor' => 'Dr. Carlos Javier Rojas Meza', 'tiempo_lectura' => '8 min'],
            ['id' => 3, 'tipo' => 'evento', 'titulo' => 'Seminario Internacional de Derecho Constitucional 2026', 'extracto' => 'La Facultad organiza el II Seminario Internacional con ponentes de Perú, Colombia y España. Inscripciones abiertas hasta el 30 de junio.', 'fecha' => '2026-04-28', 'autor' => 'Redacción FDCCPP', 'tiempo_lectura' => '4 min'],
            ['id' => 4, 'tipo' => 'noticia', 'titulo' => 'Facultad de Derecho firma convenio con el Poder Judicial de Áncash', 'extracto' => 'El acuerdo permitirá que los estudiantes realicen prácticas preprofesionales en los juzgados y salas de la Corte Superior de Justicia de Áncash.', 'fecha' => '2026-04-15', 'autor' => 'Redacción FDCCPP', 'tiempo_lectura' => '3 min'],
            ['id' => 5, 'tipo' => 'opinion', 'titulo' => 'Justicia ambiental en Áncash: deudas pendientes', 'extracto' => 'Un análisis crítico de los principales conflictos ambientales en la región y el papel que debe jugar el derecho en la protección de las comunidades afectadas.', 'fecha' => '2026-04-08', 'autor' => 'Dra. Ana Beatriz Villanueva Torres', 'tiempo_lectura' => '10 min'],
            ['id' => 6, 'tipo' => 'evento', 'titulo' => 'Conferencia: Reforma procesal penal — avances y retrocesos', 'extracto' => 'El reconocido penalista Dr. César San Martín Castro brindará una conferencia magistral en el auditorio principal de la facultad.', 'fecha' => '2026-03-20', 'autor' => 'Redacción FDCCPP', 'tiempo_lectura' => '2 min'],
        ])->map(fn ($p) => (object) [...$p, 'fecha' => Carbon::parse($p['fecha'])]);
    }

    /**
     * Personal docente.
     */
    public static function docentes(): Collection
    {
        return collect([
            ['name' => 'Dr. Carlos Javier Rojas Meza', 'area' => 'Derecho Penal', 'grado' => 'Doctor en Derecho'],
            ['name' => 'Dra. María Elena Quispe Huamán', 'area' => 'Derecho Constitucional', 'grado' => 'Doctora en Derecho'],
            ['name' => 'Mg. Jorge Alberto Sánchez Díaz', 'area' => 'Derecho Administrativo', 'grado' => 'Magíster en Derecho'],
            ['name' => 'Dra. Ana Beatriz Villanueva Torres', 'area' => 'Derecho Ambiental', 'grado' => 'Doctora en Derecho'],
            ['name' => 'Mg. Roberto Mendoza Calderón', 'area' => 'Derecho Civil', 'grado' => 'Magíster en Derecho'],
            ['name' => 'Dr. Eduardo Vargas Linares', 'area' => 'Derecho Procesal', 'grado' => 'Doctor en Derecho'],
            ['name' => 'Mg. Patricia Mendoza Ríos', 'area' => 'Derecho Laboral', 'grado' => 'Magíster en Derecho'],
            ['name' => 'Dr. Diego Alejandro Huerta Castillo', 'area' => 'Derecho y Tecnología', 'grado' => 'Doctor en Derecho'],
        ])->map(function ($d) {
            // Iniciales: primera letra del nombre + primera del último apellido (sin el título)
            $sinTitulo = Str::of($d['name'])->replaceMatches('/^(Dr\.|Dra\.|Mg\.)\s+/', '')->trim();
            $palabras = $sinTitulo->explode(' ');
            $iniciales = Str::substr($palabras->first(), 0, 1) . Str::substr($palabras->last(), 0, 1);

            return (object) [...$d, 'iniciales' => Str::upper($iniciales)];
        });
    }

    /**
     * Series estadísticas por tipo (matriculados, egresados, graduados, titulados).
     */
    public static function estadisticas(string $tipo): Collection
    {
        $series = [
            'matriculados' => [412, 438, 465, 502, 528, 551],
            'egresados' => [68, 72, 78, 85, 91, 94],
            'graduados' => [54, 61, 66, 70, 79, 88],
            'titulados' => [41, 47, 52, 58, 64, 73],
        ];

        $valores = $series[$tipo] ?? $series['matriculados'];
        $anios = ['2020', '2021', '2022', '2023', '2024', '2025'];

        return collect($valores)->map(fn ($total, $i) => (object) [
            'anio' => $anios[$i],
            'total' => $total,
        ]);
    }

    /**
     * Etiquetas legibles de cada tipo de estadística.
     *
     * @return array<string, string>
     */
    public static function tiposEstadistica(): array
    {
        return [
            'matriculados' => 'Estudiantes matriculados',
            'egresados' => 'Egresados',
            'graduados' => 'Graduados (Bachiller)',
            'titulados' => 'Titulados (Abogado)',
        ];
    }
}
