<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rider;
use App\Models\Account;
use App\Models\Assignment;
use App\Models\Forecast; // 👈 Agrega esta línea
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear el usuario administrador solo si no existe
        $adminEmail = 'admin@admin.com';
        if (!User::where('email', $adminEmail)->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make('admin@admin.com'),
                'role' => 'admin',
            ]);
            $this->command->info('Usuario Admin creado correctamente.');
        } else {
            $this->command->warn('El usuario Admin ya existe. Se omite la creación.');
        }

        // Crear riders de prueba solo si la tabla está vacía
        if (DB::table('riders')->count() === 0) {
            Rider::factory()->create([
                'full_name' => 'Rider de Prueba',
                'dni' => '12345678A',
                'email' => 'rider@rms.com',
                'password' => Hash::make('password'),
                'city' => 'L\' Briones',
                'phone' => '+34 992-693942',
                'start_date' => '1989-12-21',
                'status' => 'active',
                'weekly_contract_hours' => 20
            ]);

            Rider::factory(9)->create();

            $this->command->info('Riders de prueba creados correctamente.');
        } else {
            $this->command->warn('La tabla de riders ya contiene datos. Se omite el seeder para evitar duplicación de DNI.');
        }
        // Crear datos de forecast de prueba si la tabla está vacía
        if (DB::table('forecasts')->count() === 0) {
             Forecast::create([
                'city' => 'L\' Briones',
                'week_start_date' => now()->startOfWeek(),
                'booking_deadline' => now()->startOfWeek()->addDays(2)->setTime(12, 0),
                'forecast_data' => [
                    'mon' => ['09:00' => 5, '10:00' => 8],
                    'tue' => ['09:00' => 6, '10:00' => 7],
                    'wed' => ['09:00' => 7, '10:00' => 9],
                    'thu' => ['09:00' => 5, '10:00' => 8],
                    'fri' => ['09:00' => 10, '10:00' => 12],
                    'sat' => ['11:00' => 15, '12:00' => 20],
                    'sun' => ['11:00' => 12, '12:00' => 18],
                ]
            ]);
            $this->command->info('Forecast de prueba creado correctamente.');
        } else {
            $this->command->warn('La tabla de forecasts ya contiene datos. Se omite la creación.');
        }
    }
}
