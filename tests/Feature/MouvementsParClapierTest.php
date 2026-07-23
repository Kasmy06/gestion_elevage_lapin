<?php

namespace Tests\Feature;

use App\Livewire\Alimentation\Mouvements\Index as MouvementsIndex;
use App\Livewire\Rapports\Index as RapportsIndex;
use App\Models\Aliment;
use App\Models\Cage;
use App\Models\MouvementAliment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MouvementsParClapierTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_liste_des_mouvements_peut_etre_filtree_par_clapier(): void
    {
        $this->actingAs(User::factory()->create());

        $aliment = Aliment::factory()->create();
        $cageA = Cage::factory()->create(['numero' => 'C-A']);
        $cageB = Cage::factory()->create(['numero' => 'C-B']);

        MouvementAliment::factory()->create(['aliment_id' => $aliment->id, 'cage_id' => $cageA->id, 'type_mouvement' => 'distribution', 'quantite' => 3]);
        MouvementAliment::factory()->create(['aliment_id' => $aliment->id, 'cage_id' => $cageB->id, 'type_mouvement' => 'distribution', 'quantite' => 7]);

        // Sans filtre : les deux mouvements apparaissent.
        Livewire::test(MouvementsIndex::class)
            ->assertSee('3 kg')
            ->assertSee('7 kg');

        // Avec le filtre sur le clapier A : seul son mouvement (3 kg) apparaît.
        Livewire::test(MouvementsIndex::class)
            ->set('cageFiltre', $cageA->id)
            ->assertSee('3 kg')
            ->assertDontSee('7 kg');
    }

    public function test_le_rapport_de_distribution_par_clapier_totalise_correctement(): void
    {
        $this->actingAs(User::factory()->create());

        $aliment = Aliment::factory()->create(['nom' => 'Granulés']);
        $cage = Cage::factory()->create(['numero' => 'C-TOTAL']);

        MouvementAliment::factory()->create([
            'aliment_id' => $aliment->id,
            'cage_id' => $cage->id,
            'type_mouvement' => 'distribution',
            'quantite' => 5,
            'cout' => 1000,
        ]);
        MouvementAliment::factory()->create([
            'aliment_id' => $aliment->id,
            'cage_id' => $cage->id,
            'type_mouvement' => 'distribution',
            'quantite' => 3,
            'cout' => 600,
        ]);
        // Une entrée ne doit pas être comptée dans le total distribué.
        MouvementAliment::factory()->create([
            'aliment_id' => $aliment->id,
            'cage_id' => $cage->id,
            'type_mouvement' => 'entree',
            'quantite' => 50,
            'cout' => 5000,
        ]);

        Livewire::test(RapportsIndex::class)
            ->call('exporterDistributionParClapier')
            ->assertFileDownloaded('rapport-distribution-clapiers.csv');
    }
}
