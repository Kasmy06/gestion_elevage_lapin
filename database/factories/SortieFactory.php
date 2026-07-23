<?php

namespace Database\Factories;

use App\Models\Lapin;
use App\Models\Sortie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sortie>
 */
class SortieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lapin_id' => Lapin::factory(),
            'type' => 'vente',
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'poids_g' => $this->faker->numberBetween(2000, 3000),
            'prix' => $this->faker->randomFloat(2, 1500, 4000),
        ];
    }
}
