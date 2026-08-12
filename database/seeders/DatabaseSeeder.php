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
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Siembra inicial. Cada bloque solo corre si la tabla está vacía,
     * para que el seeder sea idempotente (no duplica contenido).
     */
    public function run(): void
    {
        if (Estadistica::count() === 0) {
            $this->call(EstadisticaSeeder::class);
        }

        // --- Fase 2: páginas institucionales ---
        if (Hito::count() === 0) {
            $this->call(HitoSeeder::class);
        }

        if (Objetivo::count() === 0) {
            $this->call(ObjetivoSeeder::class);
        }

        if (Competencia::count() === 0) {
            $this->call(CompetenciaSeeder::class);
        }

        if (AreaLaboral::count() === 0) {
            $this->call(AreaLaboralSeeder::class);
        }

        if (PerfilIngresoArea::count() === 0) {
            $this->call(PerfilIngresoAreaSeeder::class);
        }

        if (Documento::count() === 0) {
            $this->call(DocumentoSeeder::class);
        }

        // --- Fase 3: malla y organigrama ---
        if (Organigrama::count() === 0) {
            $this->call(OrganigramaSeeder::class);
        }

        if (Acceso::count() === 0) {
            $this->call(AccesoSeeder::class);
        }

        // Settings: idempotente por clave (solo crea las faltantes, no pisa ediciones).
        $this->call(SettingSeeder::class);

        // El contenido editorial y los perfiles docentes se crean y revisan
        // exclusivamente desde Filament. No se siembran demos ni publicaciones.

        // --- Usuario administrador ---
        $this->call(AdminUserSeeder::class);
    }
}
