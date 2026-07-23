<?php

namespace Database\Factories;

use App\Models\MiseBas;
use App\Models\Sevrage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sevrage>
 */
class SevrageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $miseBas = MiseBas::factory()->create();

        return [
            'mise_bas_id' => $miseBas->id,
            'date_sevrage' => $miseBas->date_sevrage_prevue,
            'nb_sevres' => $miseBas->nb_nes_vivants,
            'poids_moyen_g' => $this->faker->numberBetween(600, 900),
        ];
    }
}
