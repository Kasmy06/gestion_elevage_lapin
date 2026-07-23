<?php

namespace Database\Factories;

use App\Models\Employe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employe>
 */
class EmployeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->name(),
            'poste' => $this->faker->randomElement(['Gardien', 'Ouvrier', 'Vétérinaire']),
            'telephone' => $this->faker->phoneNumber(),
            'date_embauche' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'statut' => 'actif',
        ];
    }
}
