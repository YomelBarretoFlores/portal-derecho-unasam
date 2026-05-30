<?php

namespace Database\Seeders;

use App\Models\AreaLaboral;
use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Competencia;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Documento;
use App\Models\Estadistica;
use App\Models\Hito;
use App\Models\Objetivo;
use App\Models\PerfilIngresoArea;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Siembra inicial. Cada bloque solo corre si la tabla está vacía,
     * para que el seeder sea idempotente (no duplica contenido).
     */
    public function run(): void
    {
        // --- Fase 1: colecciones ---
        if (BlogPost::count() === 0) {
            $this->call(BlogPostSeeder::class);
        }

        if (Articulo::count() === 0) {
            $this->call(ArticuloSeeder::class);
        }

        if (Docente::count() === 0) {
            $this->call(DocenteSeeder::class);
        }

        if (Estadistica::count() === 0) {
            $this->call(EstadisticaSeeder::class);
        }

        if (Comunicado::count() === 0) {
            $this->call(ComunicadoSeeder::class);
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

        if (Setting::count() === 0) {
            $this->call(SettingSeeder::class);
        }

        // --- Usuario administrador ---
        $this->call(AdminUserSeeder::class);
    }
}
