<?php

namespace Database\Factories;

use App\Models\MiseBas;
use App\Models\Saillie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MiseBas>
 */
class MiseBasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $saillie = Saillie::factory()->create(['diagnostic_gestation' => 'positif']);

        return [
            'saillie_id' => $saillie->id,
            'femelle_id' => $saillie->femelle_id,
            'date_mise_bas' => $saillie->date_mise_bas_prevue,
            'nb_nes_vivants' => $this->faker->numberBetween(4, 9),
            'nb_morts_nes' => $this->faker->numberBetween(0, 2),
        ];
    }
}
