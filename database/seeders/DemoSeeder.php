<?php

namespace Database\Seeders;

use App\Models\Aliment;
use App\Models\Cage;
use App\Models\Lapin;
use App\Models\MiseBas;
use App\Models\MouvementAliment;
use App\Models\Pesee;
use App\Models\Race;
use App\Models\Saillie;
use App\Models\SanteIntervention;
use App\Models\Sortie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Cheptel de démonstration pour explorer l'application immédiatement :
     * quelques reproducteurs, un cycle de reproduction à chaque étape
     * (diagnostic à faire, mise bas imminente, sevrage imminent), un suivi
     * santé, une sortie et une alerte de stock bas.
     *
     * Nécessite RaceSeeder, CageSeeder et AlimentSeeder (via DatabaseSeeder).
     */
    public function run(): void
    {
        $this->call([RaceSeeder::class, CageSeeder::class, AlimentSeeder::class]);

        $neoZelandais = Race::where('nom', 'Néo-Zélandais Blanc')->first();
        $californien = Race::where('nom', 'Californien')->first();

        $male1 = $this->lapin('DEMO-M1', $neoZelandais->id, 'male', 'R-1');
        $femelle1 = $this->lapin('DEMO-F1', $neoZelandais->id, 'femelle', 'R-3');
        $femelle2 = $this->lapin('DEMO-F2', $californien->id, 'femelle', 'M-1');
        $femelle3 = $this->lapin('DEMO-F3', $californien->id, 'femelle', 'M-2');
        $jeune1 = $this->lapin('DEMO-J1', $neoZelandais->id, 'male', 'E-1', 'jeune', now()->subMonths(2));
        $engraissement1 = $this->lapin('DEMO-E1', $californien->id, 'male', null, 'engraissement', now()->subMonths(3));

        // Saillie récente : diagnostic de gestation à faire (>10 jours).
        Saillie::firstOrCreate(
            ['male_id' => $male1->id, 'femelle_id' => $femelle1->id, 'date_saillie' => Carbon::today()->subDays(15)->toDateString()],
            ['diagnostic_gestation' => 'en_attente']
        );

        // Saillie confirmée gestante : mise bas imminente.
        $saillieImminente = Saillie::firstOrCreate(
            ['male_id' => $male1->id, 'femelle_id' => $femelle2->id, 'date_saillie' => Carbon::today()->subDays(28)->toDateString()],
            [
                'diagnostic_gestation' => 'positif',
                'date_diagnostic' => Carbon::today()->subDays(18),
            ]
        );

        // Saillie déjà mise bas : sevrage imminent.
        $saillieSevrage = Saillie::firstOrCreate(
            ['male_id' => $male1->id, 'femelle_id' => $femelle3->id, 'date_saillie' => Carbon::today()->subDays(64)->toDateString()],
            [
                'diagnostic_gestation' => 'positif',
                'date_diagnostic' => Carbon::today()->subDays(54),
            ]
        );

        MiseBas::firstOrCreate(
            ['saillie_id' => $saillieSevrage->id],
            [
                'femelle_id' => $femelle3->id,
                'date_mise_bas' => Carbon::today()->subDays(33),
                'nb_nes_vivants' => 7,
                'nb_morts_nes' => 1,
            ]
        );

        // Pesées de croissance pour le jeune lapin.
        foreach ([0, 2, 4, 6] as $semaine) {
            Pesee::firstOrCreate([
                'lapin_id' => $jeune1->id,
                'date_pesee' => now()->subMonths(2)->addWeeks($semaine)->toDateString(),
            ], [
                'poids_g' => 400 + $semaine * 250,
            ]);
        }

        // Suivi santé en cours.
        SanteIntervention::firstOrCreate([
            'lapin_id' => $jeune1->id,
            'type' => 'parasite_externe',
            'libelle' => 'Gale des oreilles',
        ], [
            'date_debut' => now()->subDays(3),
            'statut' => 'en_cours',
            'traitement_applique' => "Nettoyage à l'huile + iodine dans le pavillon de l'oreille.",
        ]);

        // Sortie : vente d'un lapin d'engraissement.
        Sortie::firstOrCreate([
            'lapin_id' => $engraissement1->id,
            'type' => 'vente',
        ], [
            'date' => now()->subDays(5),
            'poids_g' => 2600,
            'prix' => 3500,
            'acheteur' => 'Marché local',
        ]);

        // Mouvement de stock qui fait passer le bloc minéral sous le seuil d'alerte.
        $blocMineral = Aliment::where('nom', 'Bloc minéral / sel')->first();
        if ($blocMineral && ! $blocMineral->stockBas()) {
            MouvementAliment::create([
                'aliment_id' => $blocMineral->id,
                'date' => now()->subDay(),
                'type_mouvement' => 'distribution',
                'quantite' => $blocMineral->stock_actuel - 0.5,
                'notes' => 'Distribution hebdomadaire',
            ]);
        }
    }

    private function lapin(
        string $identifiant,
        ?int $raceId,
        string $sexe,
        ?string $numeroCage,
        string $statut = 'reproducteur',
        ?Carbon $dateNaissance = null,
    ): Lapin {
        $cage = $numeroCage ? Cage::where('numero', $numeroCage)->first() : null;

        return Lapin::firstOrCreate(
            ['identifiant' => $identifiant],
            [
                'race_id' => $raceId,
                'sexe' => $sexe,
                'date_naissance' => $dateNaissance ?? now()->subYear(),
                'cage_id' => $cage?->id,
                'statut' => $statut,
                'origine' => 'naissance_elevage',
                'poids_actuel_g' => 3200,
            ]
        );
    }
}
