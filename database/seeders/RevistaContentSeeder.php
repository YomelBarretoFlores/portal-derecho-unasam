<?php

namespace Database\Seeders;

use App\Enums\EditorialStatus;
use App\Models\Revista;
use App\Models\RevistaContacto;
use App\Models\RevistaDocumento;
use App\Models\RevistaLineaInvestigacion;
use App\Models\RevistaMiembro;
use Illuminate\Database\Seeder;

class RevistaContentSeeder extends Seeder
{
    public function run(): void
    {
        $revista = Revista::query()->first();

        if (! $revista) {
            $revista = Revista::query()->create([
                'nombre' => 'Derecho y Cultura, Revista Científica de Derecho y Antropología Jurídica',
                'nombre_corto' => 'Derecho y Cultura',
                'presentacion' => '<p>Revista científica digital especializada en Derecho y Antropología Jurídica de la Universidad Nacional Santiago Antúnez de Mayolo.</p>',
                'enfoque_alcance' => '<p>Difunde investigaciones científicas vinculadas al Derecho y la Antropología Jurídica.</p>',
                'unidad_responsable' => 'Unidad de Investigación de la Facultad de Derecho y Ciencia Política',
                'resolucion_numero' => 'N.° 063-2026-UNASAM-FDCCPP/D.',
                'resolucion_fecha' => '2026-07-06',
                'resolucion_resumen' => 'Aprueba la creación de la revista, su equipo editorial y las normas de publicación.',
                'periodicidad' => 'Semestral',
                'modalidad' => 'Digital',
                'idiomas' => ['es', 'en'],
                'tipos_contribucion' => ['articulo_original', 'articulo_revision', 'ensayo_academico', 'resena'],
                'sistema_arbitraje' => 'Revisión por pares doble ciego',
                'norma_citacion' => 'APA 7.ª edición',
                'contacto_email' => 'revistaDC@unasam.edu.pe',
                'estado_editorial' => EditorialStatus::Published->value,
            ]);
        }

        $revista->forceFill([
            'nombre' => 'Derecho y Cultura, Revista Científica de Derecho y Antropología Jurídica',
            'nombre_corto' => 'Derecho y Cultura',
            'normas_publicacion' => $revista->normas_publicacion ?: '<h2>Política de envío y evaluación</h2><p>Los manuscritos deben ser originales, presentarse en la plantilla oficial y serán evaluados mediante revisión por pares doble ciego.</p>',
            'contenido_politicas' => $revista->contenido_politicas ?: '<p>La política editorial se incorporará una vez sea aprobada y remitida por el equipo editorial.</p>',
            'contenido_sobre' => $revista->contenido_sobre ?: ($revista->presentacion ?: '<p>Revista científica digital especializada en Derecho y Antropología Jurídica.</p>'),
            'contenido_indexacion' => $revista->contenido_indexacion ?: '<p>Proceso de indexación en curso.</p>',
            'contenido_privacidad' => $revista->contenido_privacidad ?: '<p>Contenido institucional en preparación.</p>',
            'contenido_preservacion' => $revista->contenido_preservacion ?: '<p>Contenido institucional en preparación.</p>',
            'introduccion_envios' => $revista->introduccion_envios ?: '<p>Los manuscritos se reciben mediante el formulario público. El equipo editorial verifica la documentación y responde por correo institucional.</p>',
            'facebook_url' => $revista->facebook_url ?: 'https://www.facebook.com/profile.php?id=61593094042138',
            'whatsapp_url' => $revista->whatsapp_url ?: 'https://chat.whatsapp.com/DVDjf2Yrlqu4V0wL9A0ebJ',
            'estado_editorial' => EditorialStatus::Published->value,
        ])->save();

        $documents = [
            ['norma', 'Normas de publicación', 'Documento normativo completo y autoritativo.', '/docs/revista/normas-publicacion-v1.pdf'],
            ['formato', 'Carta de presentación', 'Formato editable requerido para el envío.', '/docs/revista/carta-presentacion-v1.docx'],
            ['formato', 'Declaración de originalidad y cesión', 'Formato editable que debe presentarse firmado.', '/docs/revista/declaracion-originalidad-cesion-v1.docx'],
            ['formato', 'Plantilla editorial para artículos', 'Copia corregida conforme a las normas aprobadas.', '/docs/revista/plantilla-editorial-v1.docx'],
        ];
        foreach ($documents as [$categoria, $titulo, $descripcion, $url]) {
            RevistaDocumento::query()->updateOrCreate(
                ['revista_id' => $revista->id, 'titulo' => $titulo],
                compact('categoria', 'descripcion', 'url') + ['visible' => true, 'estado_editorial' => EditorialStatus::Published->value],
            );
        }

        $contacts = [
            ['Dra. Katherine Mónica Castro Menacho', 'Editora', 'KCASTROM@UNASAM.EDU.PE', '950061184', 'persona'],
            ['Dr. Teodorico Claudio Cristóbal Támara', 'Editor', null, '920656388', 'persona'],
            ['Revista Derecho y Cultura', 'Correo editorial', 'revistaDC@unasam.edu.pe', null, 'correo'],
            ['Revista Derecho y Cultura (correo alternativo)', 'Correo editorial recibido', 'revista-dc@unasam.edu.pe', null, 'correo'],
            ['Corrección de estilo', 'Consultas sobre corrección de estilo', null, '966184141', 'telefono'],
        ];
        foreach ($contacts as $order => [$nombre, $cargo, $email, $telefono, $tipo]) {
            RevistaContacto::query()->updateOrCreate(
                ['revista_id' => $revista->id, 'nombre' => $nombre],
                compact('cargo', 'email', 'telefono', 'tipo') + ['orden' => $order + 1, 'visible' => true],
            );
        }

        foreach ([
            ['Derecho', 'Investigaciones científicas vinculadas a las distintas áreas del Derecho.'],
            ['Antropología Jurídica', 'Investigaciones sobre pluralismo jurídico, cultura y sistemas normativos.'],
        ] as $order => [$nombre, $descripcion]) {
            RevistaLineaInvestigacion::query()->updateOrCreate(
                ['revista_id' => $revista->id, 'nombre' => $nombre],
                compact('descripcion') + ['orden' => $order + 1, 'activa' => true],
            );
        }

        $members = [
            ['director_fundador', 'PhD.', 'Félix Claudio Julca Guerrero', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['editores', 'Dra.', 'Katherine Mónica Castro Menacho', 'Universidad Nacional Santiago Antúnez de Mayolo'],
            ['editores', 'Dr.', 'Teodorico Claudio Cristóbal Támara', 'Universidad Nacional Santiago Antúnez de Mayolo'],
            ['comite_editorial', 'Dra.', 'Fany Soledad Vera Gutiérrez', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['comite_editorial', 'Dra.', 'María del Carmen Segura Córdova', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['comite_editorial', 'Dra.', 'María Candelaria Quispe Ponce', 'Tribunal Constitucional del Perú'],
            ['comite_editorial', 'Dra.', 'Olga Cristina del Rosario Gabancho León', 'Instituto de Defensa Legal y Universidad Newman, Perú'],
            ['comite_editorial', 'Dr.', 'Elmer Robles Blácido', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['comite_editorial', 'Dr.', 'Ricardo Robinson Sánchez Espinoza', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['comite_editorial', 'Dr.', 'Armando Coral Rodríguez', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['consejo_cientifico', 'Dr.', 'Fernando Molina Fernández', 'Universidad Autónoma de Madrid, España'],
            ['consejo_cientifico', 'Dr.', 'Carlos Pinacho Candelaria', 'Organización de las Naciones Unidas, EE. UU.'],
            ['consejo_cientifico', 'Dr.', 'David Lovatón Palacios', 'Pontificia Universidad Católica del Perú'],
            ['consejo_cientifico', 'Dr.', 'Pedro Paulino Grández Castro', 'Pontificia Universidad Católica del Perú y Universidad Nacional Mayor de San Marcos, Perú'],
            ['consejo_cientifico', 'Dr.', 'José Manuel Gamarra Seminario', 'Universidad Nacional de San Agustín de Arequipa, Perú'],
            ['consejo_cientifico', 'Dr.', 'Víctor William Rojas Luján', 'Universidad Nacional de Tumbes, Perú'],
            ['consejo_cientifico', 'PhD.', 'Patricia Oliart Sotomayor', 'Newcastle University, Reino Unido'],
            ['consejo_cientifico', 'PhD.', 'Ajb’ée Jiménez', 'Universidad Rafael Landívar, Guatemala'],
            ['consejo_cientifico', 'PhD.', 'Simeon Isaac Floyd', 'Universidad San Francisco de Quito, Ecuador'],
            ['consejo_cientifico', 'PhD.', 'Félix Claudio Julca Guerrero', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['consejo_revisores', 'Dr.', 'Omar Cairo Roldán', 'Pontificia Universidad Católica del Perú'],
            ['consejo_revisores', 'Dr.', 'Rubén Alfredo Quiroz Ávila', 'Universidad Nacional Mayor de San Marcos, Perú'],
            ['consejo_revisores', 'Dr.', 'Francisco Celis Mendoza', 'Universidad Nacional de San Agustín de Arequipa, Perú'],
            ['consejo_revisores', 'Dr.', 'Álvaro Masquez Salvador', 'Escuela de Formación de las Guardias Indígenas Amazónicas en la Defensa de los Derechos Humanos, Perú'],
            ['consejo_revisores', 'Dr.', 'Luis Wilfredo Robles Trejo', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['consejo_revisores', 'Dr.', 'Ronald Reagan López Julca', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['consejo_revisores', 'Mag.', 'Gina Gonzales Luna', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['consejo_revisores', 'Mag.', 'Wilmer Esteban Castillo Gamarra', 'Universidad Nacional Santiago Antúnez de Mayolo, Perú'],
            ['correctores_estilo', null, 'Sonia Nicol Saravia Durand', 'Universidad Nacional Santiago Antúnez de Mayolo'],
            ['correctores_estilo', null, 'Isaac Jonatan Morales Cerna', 'Universidad Nacional Santiago Antúnez de Mayolo'],
            ['asistentes_editoriales', null, 'Marjory Mayli Jamanca Oncoy', 'Universidad Nacional Santiago Antúnez de Mayolo'],
            ['asistentes_editoriales', null, 'Jason Jhon Ramírez Luna', 'Universidad Nacional Santiago Antúnez de Mayolo'],
        ];

        foreach ($members as $order => [$grupo, $grado, $nombre, $afiliacion]) {
            $member = RevistaMiembro::query()->firstOrNew(['revista_id' => $revista->id, 'grupo' => $grupo, 'nombre' => $nombre]);
            $member->fill(['grado' => $member->grado ?: $grado, 'afiliacion' => $member->afiliacion ?: $afiliacion, 'orden' => $member->exists ? $member->orden : $order + 1, 'activo' => true])->save();
        }
    }
}
