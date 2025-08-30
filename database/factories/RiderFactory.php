<?php

namespace Database\Factories;

use App\Models\Rider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rider>
 */
class RiderFactory extends Factory
{
    protected $model = Rider::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'dni' => fake()->unique()->numerify('########') . Str::upper(fake()->randomLetter()),
            'city' => fake()->randomElement(['GRO', 'FIG', 'MAT', 'CAL', 'BCN']),
            'phone' => fake()->unique()->e164PhoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9pE', // password
            'start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => 'active',
            'weekly_contract_hours' => fake()->numberBetween(10, 40),
            'edits_remaining' => 3,
            'schedule_is_locked' => false,
        ];
    }
}
