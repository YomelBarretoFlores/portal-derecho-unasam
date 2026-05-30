<?php

namespace Database\Seeders;

use App\Models\Competencia;
use Illuminate\Database\Seeder;

class CompetenciaSeeder extends Seeder
{
    public function run(): void
    {
        // [grupo, plan, vigente, items[ [nombre|null, texto] ]]
        $bloques = [
            ['Generales', 'Plan de Estudios 2023', true, [
                [null, 'Demuestra capacidad comunicativa en situaciones y temáticas académico-cotidianas, empleando procedimientos de análisis y síntesis en la gestión de la información y la resolución de problemas contextualizados con rigor científico.'],
                [null, 'Demuestra liderazgo y responsabilidad en el trabajo en equipo, y habilidades intra e interpersonales para generar soluciones con creatividad y visión de futuro, mediante la práctica de valores morales.'],
                [null, 'Demuestra compromiso con la calidad y su mejora continua, con responsabilidad socioambiental, respetando la diversidad social y cultural con sentido crítico y reflexivo.'],
            ]],
            ['Generales', 'Plan de Estudios 2019', false, [
                [null, 'Demuestra capacidad comunicativa adecuada y eficaz en situaciones y temáticas académico-cotidianas, y de análisis y síntesis en la gestión responsable y eficiente de la información orientada a la investigación.'],
                [null, 'Toma decisiones con rigor científico al plantear y resolver problemas en situaciones jurídicas reales.'],
                [null, 'Demuestra liderazgo y responsabilidad en el trabajo en equipo, para generar soluciones con creatividad, innovación y visión de futuro, ante situaciones y problemas del contexto de forma sistémica.'],
                [null, 'Demuestra habilidades intra e interpersonales con actitud crítica, basada en la práctica de valores morales, en el desarrollo personal y el ejercicio de la profesión.'],
                [null, 'Demuestra compromiso con la calidad y su mejora permanente, con responsabilidad social y ambiental.'],
                [null, 'Desarrolla actitudes interculturales valorando la diversidad social y cultural del entorno local, regional, nacional y mundial, con sentido crítico y reflexivo.'],
            ]],
            ['Específicas', 'Plan de Estudios 2023', true, [
                ['Asesoría y consultoría', 'Emplea las fuentes del Derecho para emitir opiniones técnicas y absolver consultas jurídicas a las instituciones públicas y privadas con responsabilidad.'],
                ['Patrocinio y resolución de conflictos', 'Aplica la dogmática y las diversas fuentes jurídicas para el patrocinio de intereses individuales y colectivos, así como la prevención de los conflictos y la solución de los mismos ante órganos jurisdiccionales y no jurisdiccionales, con empeño y ética profesional.'],
                ['Desempeño en la función pública', 'Aplica con coherencia argumentativa la dogmática jurídica para su desempeño en el sector público, con transparencia y compromiso ético.'],
                ['Investigación jurídica', 'Evidencia un manejo teórico, metodológico y tecnológico básico de la investigación jurídica para la solución de problemas en el campo dogmático, empírico e interdisciplinario, cumpliendo los parámetros exigidos por la comunidad jurídica y la ética de la investigación para su posterior publicación.'],
            ]],
            ['Específicas', 'Plan de Estudios 2019', false, [
                [null, 'Emplea con pertinencia la dogmática, la jurisprudencia y la norma jurídica en la asesoría y consultoría de las distintas instituciones públicas y privadas, con transparencia y responsabilidad, conforme exige un servicio profesional de calidad.'],
                [null, 'Maneja con capacidad argumentativa los fundamentos doctrinarios, jurisprudenciales y normativos del derecho sustantivo para la defensa de casos ante el órgano pre jurisdiccional, jurisdiccional y no jurisdiccional, acorde a las exigencias del Estado Constitucional de Derecho.'],
                [null, 'Emplea coherentemente los fundamentos doctrinarios y jurisprudenciales, así como los procedimientos que regula el derecho procesal, para resolver los conflictos de interés e incertidumbre jurídica en todos los ámbitos del derecho, conforme exige el logro del bien común y la justicia.'],
                [null, 'Aplica las doctrinas, teorías, principios lógico-jurídicos y normatividad con pertinencia argumentativa en la toma de sus decisiones funcionariales, asumiendo su compromiso responsable acorde a la vigencia del Estado Constitucional de Derecho.'],
                [null, 'Manifiesta capacidad básica en la comprensión de los fundamentos teóricos y el manejo metodológico de la investigación jurídica, valorando su utilidad para la solución de problemas jurídicos y respetando la ética de la investigación.'],
            ]],
        ];

        $orden = 0;
        foreach ($bloques as [$grupo, $plan, $vigente, $items]) {
            foreach ($items as [$nombre, $texto]) {
                Competencia::create([
                    'grupo' => $grupo,
                    'plan' => $plan,
                    'vigente' => $vigente,
                    'nombre' => $nombre,
                    'texto' => $texto,
                    'orden' => $orden++,
                ]);
            }
        }
    }
}
