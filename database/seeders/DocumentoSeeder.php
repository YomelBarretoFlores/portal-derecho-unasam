<?php

namespace Database\Seeders;

use App\Models\Documento;
use Illuminate\Database\Seeder;

class DocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $documentos = [
            ['Plan de Estudios 2023', 'Planes de Estudio', '2023-03-01', 'https://sga.unasam.edu.pe/res/mallas_firmadas/DERECHO%20Y%20CIENCIAS%20POL%C3%8DTICAS.pdf'],
            ['Plan de Estudios 2019', 'Planes de Estudio', '2019-03-01', null],
            ['Reglamento de Grados y Títulos', 'Reglamentos', '2022-08-15', null],
            ['Reglamento de Prácticas Preprofesionales', 'Reglamentos', '2022-08-15', null],
            ['R. de A.U. N.º 007-2018-UNASAM', 'Resoluciones', '2018-09-12', null],
            ['R. N.º 001-2015-AU-UNASAM', 'Resoluciones', '2015-01-22', null],
        ];

        foreach ($documentos as $i => [$titulo, $categoria, $fecha, $url]) {
            Documento::create([
                'titulo' => $titulo,
                'categoria' => $categoria,
                'fecha' => $fecha,
                'url' => $url,
                'orden' => $i,
            ]);
        }
    }
}
