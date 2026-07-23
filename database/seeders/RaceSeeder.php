<?php

namespace Database\Seeders;

use App\Models\Race;
use Illuminate\Database\Seeder;

class RaceSeeder extends Seeder
{
    /**
     * Races citées dans l'Agrodok 20 (L'élevage familial de lapins
     * dans les zones tropicales), chapitre 2.
     */
    public function run(): void
    {
        $races = [
            [
                'nom' => 'Polonais',
                'categorie' => 'naine',
                'poids_min_kg' => 1.0,
                'poids_max_kg' => 1.5,
                'description' => "Race naine, pèse jusqu'à 1,5 kg.",
            ],
            [
                'nom' => 'Hollandais',
                'categorie' => 'legere',
                'poids_min_kg' => 2.0,
                'poids_max_kg' => 3.0,
                'description' => 'Race légère, 2 à 3 kg.',
            ],
            [
                'nom' => 'Néo-Zélandais Blanc',
                'categorie' => 'moyenne',
                'poids_min_kg' => 3.0,
                'poids_max_kg' => 5.0,
                'description' => "Race moyenne recommandée pour la production familiale de viande : bonne fertilité, croissance rapide.",
            ],
            [
                'nom' => 'Californien',
                'categorie' => 'moyenne',
                'poids_min_kg' => 3.0,
                'poids_max_kg' => 5.0,
                'description' => "Race moyenne recommandée pour la production familiale de viande : bonne fertilité, croissance rapide.",
            ],
            [
                'nom' => 'Géant des Flandres',
                'categorie' => 'lourde',
                'poids_min_kg' => 5.0,
                'poids_max_kg' => 9.0,
                'description' => "Race lourde. Attention : fertilité moindre, petites portées, sensible aux maux de pattes.",
            ],
        ];

        foreach ($races as $race) {
            Race::updateOrCreate(['nom' => $race['nom']], $race);
        }
    }
}
