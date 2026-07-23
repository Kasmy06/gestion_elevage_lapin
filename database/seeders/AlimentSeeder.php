<?php

namespace Database\Seeders;

use App\Models\Aliment;
use Illuminate\Database\Seeder;

class AlimentSeeder extends Seeder
{
    /**
     * Aliments cités au chapitre 7 de l'Agrodok.
     */
    public function run(): void
    {
        $aliments = [
            ['nom' => 'Son de riz', 'type' => 'concentre', 'unite' => 'kg', 'stock_actuel' => 50, 'seuil_alerte' => 10],
            ['nom' => 'Granulés concentrés', 'type' => 'concentre', 'unite' => 'kg', 'stock_actuel' => 30, 'seuil_alerte' => 10],
            ['nom' => 'Fourrage vert', 'type' => 'fourrage', 'unite' => 'kg', 'stock_actuel' => 20, 'seuil_alerte' => 5],
            ['nom' => 'Bloc minéral / sel', 'type' => 'mineraux', 'unite' => 'kg', 'stock_actuel' => 5, 'seuil_alerte' => 1],
        ];

        foreach ($aliments as $aliment) {
            Aliment::updateOrCreate(['nom' => $aliment['nom']], $aliment);
        }
    }
}
