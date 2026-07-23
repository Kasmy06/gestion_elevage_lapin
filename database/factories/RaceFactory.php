<?php

namespace Database\Factories;

use App\Models\Race;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Race>
 */
class RaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => ucfirst($this->faker->unique()->word()),
            'categorie' => $this->faker->randomElement(['naine', 'legere', 'moyenne', 'lourde']),
            'poids_min_kg' => $this->faker->randomFloat(2, 1, 4),
            'poids_max_kg' => $this->faker->randomFloat(2, 4, 9),
            'description' => $this->faker->sentence(),
        ];
    }
}
