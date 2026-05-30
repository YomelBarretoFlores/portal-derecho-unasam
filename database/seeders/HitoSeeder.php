<?php

namespace Database\Seeders;

use App\Models\Hito;
use Illuminate\Database\Seeder;

class HitoSeeder extends Seeder
{
    public function run(): void
    {
        $hitos = [
            ['1986', 'Creación de la Escuela', 'Mediante R.R. N.º 438-86-UNASAM (1 de setiembre de 1986) se crea la Facultad de Letras, con la Escuela de Formación Profesional de Derecho y Ciencias Políticas.'],
            ['1993', 'Categoría de Facultad', 'Por R.R. N.º 594-93-UNASAM (11 de noviembre de 1993) se suprime la Facultad de Letras y se eleva la Escuela de Derecho y Ciencias Políticas a la categoría de Facultad.'],
            ['2015', 'Nuevo Estatuto', 'La Resolución N.º 001-2015-AU-UNASAM (22 de enero de 2015) aprueba el nuevo Estatuto, que reconoce la Facultad y la Escuela Profesional de Derecho y Ciencias Políticas.'],
            ['2017', 'Modificación del Estatuto', 'Por R. de Asamblea Universitaria N.º 051-2017-UNASAM (20 de setiembre de 2017) se modifica el art. 28.º: «Escuela Profesional de Derecho».'],
            ['2018', 'Creación de la Carrera', 'La R. de Asamblea Universitaria N.º 007-2018-UNASAM (12 de setiembre de 2018) aprueba, con efecto anticipado al 1 de setiembre de 1986, la creación de la Carrera Profesional de Derecho y Ciencias Políticas.'],
            ['2019', 'Plan de Estudios 2019', 'Entra en vigencia el plan curricular 2019, en proceso de actualización durante el año 2024.'],
            ['2023', 'Plan de Estudios 2023', 'Entra en vigencia el nuevo plan de estudios de la carrera, basado en un enfoque por competencias.'],
        ];

        foreach ($hitos as $i => [$anio, $titulo, $descripcion]) {
            Hito::create([
                'anio' => (int) $anio,
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'orden' => $i,
            ]);
        }
    }
}
