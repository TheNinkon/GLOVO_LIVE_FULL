<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear 1 Administrador de prueba
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@rms.com',
            'password' => Hash::make('password123'),
        ]);

        // Crear 10 Riders de prueba
        Rider::factory(1)->create();

        // Puedes añadir un rider específico para que sea fácil de recordar
        Rider::factory()->create([
            'full_name' => 'Rider de Prueba',
            'dni' => '12345678A',
            'email' => 'rider@rms.com',
            'password' => Hash::make('password'),
        ]);
    }
}
