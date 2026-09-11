<?php

namespace Database\Seeders;

use App\Models\Acceso;
use App\Models\AreaLaboral;
use App\Models\Competencia;
use App\Models\Documento;
use App\Models\Estadistica;
use App\Models\Hito;
use App\Models\Objetivo;
use App\Models\Organigrama;
use App\Models\PerfilIngresoArea;
use App\Models\Revista;
use Illuminate\Database\Seeder;

/**
 * Base institucional del portal: lo que tiene que existir para que el sitio
 * no salga vacío el primer día.
 *
 * Está separado del usuario administrador porque son dos cosas con riesgos
 * distintos. Esto no necesita ninguna variable de entorno y se puede ejecutar
 * en cada despliegue sin peligro: cada bloque solo actúa si su tabla está
 * vacía, y los ajustes solo crean las claves que falten, sin pisar ediciones.
 *
 * El contenido editorial —revista, números, artículos, perfiles docentes— NO
 * se siembra aquí. Se crea y se revisa desde el panel, que es su única fuente
 * de verdad. Véase docs/CARGA_INICIAL_CMS.md.
 */
class ContenidoInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        $bloques = [
            Estadistica::class => EstadisticaSeeder::class,
            Hito::class => HitoSeeder::class,
            Objetivo::class => ObjetivoSeeder::class,
            Competencia::class => CompetenciaSeeder::class,
            AreaLaboral::class => AreaLaboralSeeder::class,
            PerfilIngresoArea::class => PerfilIngresoAreaSeeder::class,
            Documento::class => DocumentoSeeder::class,
            Organigrama::class => OrganigramaSeeder::class,
            Acceso::class => AccesoSeeder::class,
        ];

        foreach ($bloques as $modelo => $seeder) {
            if ($modelo::count() === 0) {
                $this->call($seeder);
            }
        }

        // Idempotente por clave: crea las que falten, no toca las existentes.
        $this->call(SettingSeeder::class);

        // El micrositio de la revista SOLO se importa en una instalación nueva.
        //
        // Repetirlo no es inofensivo, aunque no duplique filas. RevistaContentSeeder
        // es una importación autoritativa: reescribe el nombre de la revista, fuerza
        // su estado editorial a «publicado» y sobrescribe con updateOrCreate el
        // cargo, el correo y el teléfono de cada contacto. Ejecutado en cada
        // despliegue revertiría el trabajo del equipo editorial —incluida la
        // decisión deliberada de dejar la revista sin publicar— sin avisar a nadie.
        if (Revista::count() === 0) {
            $this->call(RevistaContentSeeder::class);
        }
    }
}
