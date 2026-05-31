<?php

namespace Database\Seeders;

use App\Models\Organigrama;
use Illuminate\Database\Seeder;

class OrganigramaSeeder extends Seeder
{
    public function run(): void
    {
        // Fila única (singleton). El usuario sube la imagen desde el panel.
        Organigrama::firstOrCreate([], [
            'titulo' => 'Organigrama',
            'descripcion' => null,
        ]);
    }
}
