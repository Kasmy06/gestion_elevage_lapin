<?php

namespace Database\Seeders;

use App\Models\Cage;
use Illuminate\Database\Seeder;

class CageSeeder extends Seeder
{
    public function run(): void
    {
        $cages = [
            ['numero' => 'M-1', 'emplacement' => 'Étable', 'type' => 'maternite', 'capacite' => 1],
            ['numero' => 'M-2', 'emplacement' => 'Étable', 'type' => 'maternite', 'capacite' => 1],
            ['numero' => 'R-1', 'emplacement' => 'Étable', 'type' => 'individuelle', 'capacite' => 1],
            ['numero' => 'R-2', 'emplacement' => 'Étable', 'type' => 'individuelle', 'capacite' => 1],
            ['numero' => 'R-3', 'emplacement' => 'Étable', 'type' => 'individuelle', 'capacite' => 1],
            ['numero' => 'E-1', 'emplacement' => 'Étable', 'type' => 'engraissement', 'capacite' => 6],
            ['numero' => 'E-2', 'emplacement' => 'Étable', 'type' => 'engraissement', 'capacite' => 6],
            ['numero' => 'Q-1', 'emplacement' => 'À l\'écart', 'type' => 'quarantaine', 'capacite' => 2],
        ];

        foreach ($cages as $cage) {
            Cage::updateOrCreate(['numero' => $cage['numero']], $cage);
        }
    }
}
