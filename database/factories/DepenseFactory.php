<?php

namespace Database\Factories;

use App\Models\Depense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Depense>
 */
class DepenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categorie' => $this->faker->randomElement(['alimentation', 'sante', 'equipement', 'main_oeuvre', 'transport', 'autre']),
            'libelle' => $this->faker->sentence(3),
            'montant' => $this->faker->randomFloat(2, 1000, 50000),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
