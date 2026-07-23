<?php

namespace Database\Factories;

use App\Models\Lapin;
use App\Models\SanteIntervention;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SanteIntervention>
 */
class SanteInterventionFactory extends Factory
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
            'type' => $this->faker->randomElement(['maladie', 'traitement', 'vaccination', 'parasite_externe']),
            'libelle' => $this->faker->randomElement(['Coccidiose', 'Gale des oreilles', 'Pasteurellose', 'Vermifuge']),
            'date_debut' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'statut' => 'en_cours',
        ];
    }
}
