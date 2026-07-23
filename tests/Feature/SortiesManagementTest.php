<?php

namespace Tests\Feature;

use App\Livewire\Sorties\Index as SortiesIndex;
use App\Models\Cage;
use App\Models\Lapin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class SortiesManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_enregistrer_une_sortie(): void
    {
        $this->actingAs(User::factory()->create());
        $lapin = Lapin::factory()->create(['statut' => 'engraissement']);

        Livewire::test(SortiesIndex::class)
            ->call('creer')
            ->set('lapin_id', $lapin->id)
            ->set('type', 'vente')
            ->set('date', Carbon::today()->toDateString())
            ->set('prix', 3000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sorties', [
            'lapin_id' => $lapin->id,
            'type' => 'vente',
            'prix' => 3000,
        ]);
    }

    public function test_une_sortie_change_automatiquement_le_statut_du_lapin_et_libere_sa_cage(): void
    {
        $this->actingAs(User::factory()->create());
        $cage = Cage::factory()->create();
        $lapin = Lapin::factory()->create(['statut' => 'engraissement', 'cage_id' => $cage->id]);

        Livewire::test(SortiesIndex::class)
            ->call('creer')
            ->set('lapin_id', $lapin->id)
            ->set('type', 'mort')
            ->set('date', Carbon::today()->toDateString())
            ->set('cause', 'Coup de chaleur')
            ->call('save')
            ->assertHasNoErrors();

        $lapin->refresh();

        $this->assertSame('mort', $lapin->statut);
        $this->assertNull($lapin->cage_id);
    }

    public function test_un_lapin_sorti_napparait_plus_dans_la_liste_des_lapins_disponibles_pour_une_sortie(): void
    {
        $this->actingAs(User::factory()->create());
        Lapin::factory()->create(['identifiant' => 'L-ACTIF', 'statut' => 'engraissement']);
        Lapin::factory()->create(['identifiant' => 'L-VENDU', 'statut' => 'vendu']);

        Livewire::test(SortiesIndex::class)
            ->call('creer')
            ->assertSee('L-ACTIF')
            ->assertDontSee('L-VENDU');
    }
}
