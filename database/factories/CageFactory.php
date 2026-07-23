<?php

namespace Database\Factories;

use App\Models\Cage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cage>
 */
class CageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero' => 'C-'.$this->faker->unique()->numberBetween(1, 9999),
            'emplacement' => $this->faker->randomElement(['Étable A', 'Étable B', 'Extérieur']),
            'type' => $this->faker->randomElement(['individuelle', 'maternite', 'engraissement', 'quarantaine']),
            'capacite' => 1,
            'notes' => null,
        ];
    }
}
