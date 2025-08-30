<?php

namespace Database\Seeders;

use App\Models\Rider;
use App\Models\Forecast;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $cities = ['GRO', 'FIG', 'MAT', 'CAL', 'BCN'];

        // Crear riders de ejemplo para cada ciudad
        foreach ($cities as $city) {
            Rider::factory()->create([
                'name' => "Rider {$city}",
                'email' => "rider.{$city}@example.com",
                'password' => bcrypt('password'),
                'city' => $city,
                'weekly_contract_hours' => 20,
                'edits_remaining' => 3,
                'schedule_is_locked' => false,
            ]);
        }

        // Crear un forecast de ejemplo para la semana actual para cada ciudad
        $today = Carbon::now()->startOfWeek(Carbon::MONDAY);
        foreach ($cities as $city) {
            Forecast::create([
                'city' => $city,
                'week_start_date' => $today,
                'booking_deadline' => $today->copy()->addDays(2),
                'forecast_data' => [
                    'mon' => ['09:00' => 5, '10:00' => 10],
                    'tue' => ['09:00' => 6, '10:00' => 12],
                    'wed' => ['09:00' => 7, '10:00' => 15],
                    'thu' => ['09:00' => 8, '10:00' => 18],
                    'fri' => ['09:00' => 10, '10:00' => 20],
                    'sat' => ['09:00' => 12, '10:00' => 22],
                    'sun' => ['09:00' => 15, '10:00' => 25],
                ],
            ]);
        }
    }
}
