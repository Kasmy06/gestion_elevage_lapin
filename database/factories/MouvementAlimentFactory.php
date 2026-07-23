<?php

namespace Database\Factories;

use App\Models\Aliment;
use App\Models\MouvementAliment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MouvementAliment>
 */
class MouvementAlimentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'aliment_id' => Aliment::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'type_mouvement' => $this->faker->randomElement(['entree', 'distribution']),
            'quantite' => $this->faker->randomFloat(2, 1, 20),
        ];
    }
}
