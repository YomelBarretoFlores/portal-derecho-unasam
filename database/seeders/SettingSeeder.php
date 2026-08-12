<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $valores = [
            'presentacion_titulo' => 'Bienvenido al Programa de Estudio de Derecho y Ciencias Políticas',
            'presentacion_cuerpo' => implode("\n\n", [
                'Esta carrera forma al profesional liberal competente y capaz de aportar a la sociedad los componentes sociales de justicia, equidad, estado de derecho y paz social, apelando a su actuación como científico social que utiliza tecnología actualizada y humanísticamente pertinente.',
                'El abogado forjado en la UNASAM se caracteriza por sus rasgos conductuales coherentes con su formación académica y doctrinaria en función de la calidad de vida justa de todos los ciudadanos.',
                'La Facultad de Derecho y Ciencias Políticas ha cumplido sus «Bodas de Perla» precisamente el año 2016, son 30 años de vida institucional que ha significado evaluar muchos logros en nuestro desarrollo social y físico, habiendo mantenido un estándar de crecimiento en su población estudiantil, dada la gran demanda que existe por la carrera de abogacía en nuestro medio.',
            ]),
            'datos_programa' => implode("\n", [
                'Facultad | Derecho y Ciencias Políticas',
                'Grado académico | Bachiller en Derecho',
                'Título profesional | Abogado(a)',
                'Duración | 5 años (10 ciclos)',
                'Modalidad | Presencial',
                'Sede | Huaraz, Áncash',
            ]),
            'mision' => 'Formar abogados líderes, con sólida formación científica, humanística, jurídica, ética e inclusiva, comprometidos con la investigación, la defensa de los derechos humanos, la justicia y el desarrollo sostenible, capaces de responder con responsabilidad a los desafíos de la realidad social y profesional, tanto a nivel local como global.',
            'vision' => 'Consolidarse como un programa de estudios acreditado y de referencia nacional e internacional en la formación de abogados íntegros, con sólida preparación científica, humanística y orientada a la investigación, comprometidos con la justicia, la conciencia social, el desarrollo sostenible y la defensa del Estado Constitucional de Derecho, capaces de enfrentar los retos de un entorno globalizado.',
            'historia_trayectoria_titulo' => 'Nuestra trayectoria',
            'historia_trayectoria_cuerpo' => implode("\n\n", [
                'La Escuela Profesional de Derecho y Ciencias Políticas responde a las necesidades y expectativas de la sociedad. Está representada por su director de Escuela y cuenta con una plana docente calificada, adscrita a los Departamentos Académicos de Derecho y Ciencias Políticas.',
                'Desde su creación, la Escuela estuvo orientada a ofrecer estudios presenciales, con un enfoque integral que busca no solo impartir conocimientos técnicos, sino también formar abogados comprometidos con los valores éticos y sociales.',
                'Su misión ha sido preparar profesionales capaces de afrontar los retos de la modernidad, adaptándose a las cambiantes demandas del entorno jurídico y social, promoviendo soluciones innovadoras a los problemas contemporáneos. Además, se ha centrado en cerrar las brechas de justicia, fomentando en sus egresados un fuerte sentido de responsabilidad social para contribuir a la equidad y al acceso a la justicia para todos los sectores de la sociedad.',
                'Actualmente se vienen implementando la biblioteca automatizada, el centro de conciliación y arbitraje, el consultorio jurídico gratuito y otros escenarios educativos que promueven el aprendizaje significativo. A la fecha, el programa cuenta con dos planes curriculares: el Plan 2019 vigente —en actualización durante 2024— y el Plan 2023, basado en un enfoque por competencias.',
            ]),
            'resumen_titulo' => 'Currículo flexible y por competencias',
            'resumen_cuerpo' => implode("\n\n", [
                'El Plan de Estudios, como parte del currículo universitario, se caracteriza por ser flexible: nos permite modificaciones en función de la diversidad humana y social, y de las particularidades, necesidades e intereses de los estudiantes de acuerdo a su contexto en particular.',
                'El modelo curricular que orienta la actualización está basado en el enfoque por competencias —generales y específicas— con asignaturas generales, específicas y de especialidad. Asimismo, se ha considerado la flexibilidad curricular mediante cursos electivos.',
            ]),
            'resumen_cita1' => '«Cada universidad determina el diseño curricular de cada especialidad, en los niveles de enseñanza respectivos, de acuerdo a las necesidades nacionales y regionales que contribuyan al desarrollo del país. […] Cada universidad determina en la estructura curricular el nivel de estudios de pregrado, la pertinencia y duración de las prácticas preprofesionales, de acuerdo a sus especialidades. […] El currículo se debe actualizar cada tres (3) años o cuando sea conveniente, según los avances científicos y tecnológicos.» (Énfasis nuestro.)',
            'resumen_cita2' => 'el currículo de cada carrera profesional se debe actualizar cada tres (03) años, según los avances científicos y tecnológicos o cuando resulte necesario y/o conveniente. El desarrollo curricular debe ser evaluado cada año por la Comisión respectiva. Los estudiantes inician y terminan con un currículo único.',
            'resumen_cierre' => 'La Ley Universitaria N.º 30220 del Perú, en su artículo 40, establece la importancia de la evaluación y actualización continua de los planes de estudio, lo que es fundamental para asegurar la calidad educativa y la pertinencia de la formación profesional.',
            'perfil_ingreso_especifico' => 'Evidencia actitud motivadora y aptitud significativa con claridad para perseguir estudios en el Programa de Derecho y Ciencias Políticas.',
            'perfil_egreso_2023' => implode("\n\n", [
                'El egresado de la carrera de Derecho es un profesional con formación jurídica sólida, capaz de asesorar y emitir opiniones legales con responsabilidad y ética, aplicando con criterio las fuentes del Derecho en la resolución de conflictos y en el patrocinio de intereses individuales y colectivos ante diversas instancias.',
                'Cuenta con competencias en investigación jurídica, comunicación efectiva, trabajo en equipo y pensamiento crítico, actuando con liderazgo, compromiso social y respeto por la diversidad cultural. Su desempeño profesional se orienta a la calidad, la transparencia y la mejora continua, contribuyendo al desarrollo de una sociedad más justa y democrática.',
            ]),
            'perfil_egreso_2019' => implode("\n\n", [
                'El egresado de la carrera de Derecho es un profesional con sólida formación jurídica, ética y humanista, capaz de asesorar, defender y tomar decisiones fundamentadas en el marco del Estado Constitucional de Derecho.',
                'Posee habilidades de comunicación, investigación, argumentación y resolución de conflictos jurídicos, actuando con responsabilidad, liderazgo y compromiso social. Valora la diversidad cultural, promueve la justicia y contribuye al bien común con una visión crítica, innovadora y orientada a la calidad.',
            ]),

            // --- Fase 3: Inicio ---
            'home_hero_titulo' => 'Derecho y Ciencias Políticas',
            'home_hero_subtitulo' => 'Formación jurídica de excelencia con responsabilidad social, al servicio de Áncash y el país desde 1986.',
            'home_hero_cta1' => 'Conoce el programa',
            'home_hero_cta2' => 'Plan de Estudios',
            'home_hero_stat1_label' => 'Años de trayectoria',
            'home_hero_stat1_valor' => '40',
            'home_hero_stat1_sufijo' => '+',
            'home_hero_stat2_label' => 'Áreas del Derecho',
            'home_hero_stat2_valor' => '8',
            'home_hero_stat2_sufijo' => '',
            'home_about_eyebrow' => 'El programa',
            'home_about_titulo' => 'Formando profesionales del derecho desde 1986',
            'home_about_cuerpo' => implode("\n\n", [
                'El Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM forma abogados con sólida base jurídica, sentido ético y compromiso con el desarrollo de la región Áncash y del país.',
                'Nuestro plan de estudios combina la formación teórica con la práctica profesional, la investigación jurídica y la responsabilidad social, preparando a los estudiantes para los desafíos del ejercicio del derecho en el siglo XXI.',
            ]),
            'home_about_cita' => 'Del esfuerzo de sus hijos, depende el progreso de los pueblos.',
            'home_accesos_eyebrow' => 'Accesos directos',
            'home_accesos_titulo' => 'Explora el programa',
            'home_stats_eyebrow' => 'El programa en cifras',
            'home_stats_titulo' => 'Una comunidad académica en crecimiento',
            'home_stats_narrativa' => 'En 2024 alcanzamos 1049 matriculados y 68 titulados, con más de 40 años formando abogados en 8 áreas del derecho.',
            'home_revista_eyebrow' => 'Revista Derecho y Cultura',
            'home_revista_titulo' => 'Investigación jurídica original',
            'home_blog_eyebrow' => 'Actualidad',
            'home_blog_titulo' => 'Noticias, opiniones y eventos',
            'home_marquee' => 'Derecho Civil · Derecho Penal · Derecho Constitucional · Derecho Laboral · Derecho Administrativo · Derecho Procesal · Derecho Internacional · Derecho Comercial',

            // --- Fase 3: Footer / contacto / SEO ---
            'footer_marca' => 'Derecho y Ciencias Políticas',
            'footer_descripcion' => 'Programa de Estudios de la Universidad Nacional Santiago Antúnez de Mayolo.',
            'contacto_direccion' => 'Ciudad Universitaria, Huaraz, Áncash',
            'contacto_telefono' => '(043) 640-020',
            'contacto_email' => 'mesadepartesdigital@unasam.edu.pe',
            'footer_cta_texto' => 'Portal UNASAM',
            'footer_cta_url' => 'https://unasam.edu.pe',
            'lema' => 'Orabunt Causas Melius',
            'seo_title' => 'Derecho y Ciencias Políticas — UNASAM',
            'seo_description' => 'Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional Santiago Antúnez de Mayolo — Huaraz, Áncash, Perú.',

            // --- Fase 3: Plan de Estudios ---
            'plan_grado' => 'Bachiller en Derecho',
            'plan_titulo_prof' => 'Abogado(a)',
            'plan_modalidad' => 'Presencial',
            'plan_pdf_url' => 'https://sga.unasam.edu.pe/res/mallas_firmadas/DERECHO%20Y%20CIENCIAS%20POL%C3%8DTICAS.pdf',
            'plan_sga_url' => 'https://sga.unasam.edu.pe/escuela/16/plancurricular/06',
            'plan_intro' => '',
        ];

        // Solo crea las claves que falten; NO pisa lo que el usuario ya editó.
        $existentes = Setting::query()->pluck('clave')->all();

        foreach ($valores as $clave => $valor) {
            if (! in_array($clave, $existentes, true)) {
                Setting::set($clave, $valor);
            }
        }
    }
}
