<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'barretofloresyomeljair@gmail.com';

        if (User::where('email', $email)->exists()) {
            $this->command?->info("El usuario admin ya existe: {$email}");

            return;
        }

        $password = Str::password(16);

        User::create([
            'name' => 'Yomel Barreto',
            'email' => $email,
            'password' => $password, // el cast 'hashed' del modelo lo encripta
        ]);

        $this->command?->warn('================ USUARIO ADMIN CREADO ================');
        $this->command?->warn("Email:    {$email}");
        $this->command?->warn("Password: {$password}");
        $this->command?->warn('Guárdala y cámbiala tras iniciar sesión en /admin');
        $this->command?->warn('======================================================');
    }
}
