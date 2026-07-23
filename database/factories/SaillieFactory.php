<?php

namespace Database\Factories;

use App\Models\Lapin;
use App\Models\Saillie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Saillie>
 */
class SaillieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateSaillie = $this->faker->dateTimeBetween('-2 months', 'now');

        return [
            'male_id' => Lapin::factory()->reproducteur('male'),
            'femelle_id' => Lapin::factory()->reproducteur('femelle'),
            'date_saillie' => $dateSaillie,
            'diagnostic_gestation' => 'en_attente',
        ];
    }
}
