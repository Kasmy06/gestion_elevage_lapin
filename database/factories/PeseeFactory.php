<?php

namespace Database\Factories;

use App\Models\Lapin;
use App\Models\Pesee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pesee>
 */
class PeseeFactory extends Factory
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
            'date_pesee' => $this->faker->dateTimeBetween('-2 months', 'now'),
            'poids_g' => $this->faker->numberBetween(500, 4000),
        ];
    }
}
