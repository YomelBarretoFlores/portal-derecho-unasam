<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $replacements = [
            'contenido_politicas' => [
                '<p>La política editorial se incorporará una vez sea aprobada y remitida por el equipo editorial.</p>',
                '<p>Derecho y Cultura recibe trabajos originales vinculados al Derecho y la Antropología Jurídica. Los manuscritos se someten a verificación editorial y revisión por pares doble ciego de acuerdo con las normas para autores.</p>',
            ],
            'contenido_indexacion' => [
                '<p>Proceso de indexación en curso.</p>',
                '<p>Derecho y Cultura es una revista científica digital de periodicidad semestral de la Universidad Nacional Santiago Antúnez de Mayolo.</p>',
            ],
            'contenido_privacidad' => [
                '<p>Contenido institucional en preparación.</p>',
                '<p>Derecho y Cultura protege la información de autores y colaboradores durante la gestión editorial. Actualmente no se recopilan datos personales mediante formularios públicos.</p>',
            ],
            'contenido_preservacion' => [
                '<p>Contenido institucional en preparación.</p>',
                '<p>Los números publicados de Derecho y Cultura se conservan en formato PDF y permanecen disponibles para consulta y descarga en el archivo editorial del portal.</p>',
            ],
        ];

        foreach ($replacements as $field => [$old, $new]) {
            DB::table('revistas')
                ->where(fn ($query) => $query->whereNull($field)->orWhere($field, '')->orWhere($field, $old))
                ->update([$field => $new]);
        }

        DB::table('revista_documentos')
            ->where('titulo', 'Plantilla editorial para artículos')
            ->where(fn ($query) => $query->whereNull('descripcion')->orWhere('descripcion', 'Copia corregida conforme a las normas aprobadas.'))
            ->update(['descripcion' => 'Documento editable para redactar y presentar artículos.']);
    }

    public function down(): void
    {
        // Los textos públicos no se revierten para no reintroducir notas internas.
    }
};
