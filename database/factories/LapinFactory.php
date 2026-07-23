<?php

namespace Database\Factories;

use App\Models\Lapin;
use App\Models\Race;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lapin>
 */
class LapinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identifiant' => 'L-'.$this->faker->unique()->numberBetween(1, 99999),
            'race_id' => Race::factory(),
            'sexe' => $this->faker->randomElement(['male', 'femelle']),
            'date_naissance' => $this->faker->dateTimeBetween('-2 years', '-2 months'),
            'statut' => 'jeune',
            'origine' => 'naissance_elevage',
            'poids_actuel_g' => $this->faker->numberBetween(800, 4500),
        ];
    }

    public function reproducteur(string $sexe): static
    {
        return $this->state(fn () => [
            'sexe' => $sexe,
            'statut' => 'reproducteur',
            'date_naissance' => $this->faker->dateTimeBetween('-2 years', '-8 months'),
        ]);
    }
}
