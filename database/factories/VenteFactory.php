<?php

namespace Database\Factories;

use App\Models\Vente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vente>
 */
class VenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantite = $this->faker->numberBetween(1, 5);
        $prixUnitaire = $this->faker->randomFloat(2, 1000, 5000);

        return [
            'description' => $this->faker->randomElement(['Lapin adulte', 'Peau tannée', 'Fumier']),
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire,
            'montant_total' => $quantite * $prixUnitaire,
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'mode_paiement' => 'especes',
            'statut_paiement' => 'paye',
        ];
    }
}
