<?php

namespace Database\Factories;

use App\Models\Aliment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Aliment>
 */
class AlimentFactory extends Factory
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
            'type' => $this->faker->randomElement(['fourrage', 'concentre', 'mineraux', 'autre']),
            'unite' => 'kg',
            'stock_actuel' => $this->faker->randomFloat(2, 0, 100),
            'seuil_alerte' => 10,
        ];
    }
}
