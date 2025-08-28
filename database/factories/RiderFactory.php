<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class RiderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'dni' => $this->faker->unique()->numerify('########').$this->faker->randomLetter(),
            'city' => $this->faker->city(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('1234'), // Contraseña por defecto para todos
            'start_date' => $this->faker->date(),
            'status' => 'active',
        ];
    }
}
