<?php

namespace Tests\Feature;

use App\Livewire\Sante\Index as SanteIndex;
use App\Models\Lapin;
use App\Models\SanteIntervention;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class SanteManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_enregistrer_un_suivi_sante(): void
    {
        $this->actingAs(User::factory()->create());
        $lapin = Lapin::factory()->create();

        Livewire::test(SanteIndex::class)
            ->call('creer')
            ->set('lapin_id', $lapin->id)
            ->set('type', 'maladie')
            ->set('libelle', 'Coryza')
            ->set('date_debut', Carbon::today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sante_interventions', [
            'lapin_id' => $lapin->id,
            'libelle' => 'Coryza',
            'statut' => 'en_cours',
        ]);
    }

    public function test_un_suivi_sante_peut_etre_cloture_comme_gueri(): void
    {
        $this->actingAs(User::factory()->create());
        $intervention = SanteIntervention::factory()->create(['statut' => 'en_cours']);

        Livewire::test(SanteIndex::class)
            ->call('cloturer', $intervention->id, 'gueri');

        $this->assertSame('gueri', $intervention->fresh()->statut);
        $this->assertNotNull($intervention->fresh()->date_fin);
    }

    public function test_un_suivi_sante_peut_etre_cloture_comme_deces(): void
    {
        $this->actingAs(User::factory()->create());
        $intervention = SanteIntervention::factory()->create(['statut' => 'en_cours']);

        Livewire::test(SanteIndex::class)
            ->call('cloturer', $intervention->id, 'deces');

        $this->assertSame('deces', $intervention->fresh()->statut);
    }

    public function test_la_liste_des_suivis_sante_peut_etre_filtree_par_statut(): void
    {
        $this->actingAs(User::factory()->create());
        SanteIntervention::factory()->create(['statut' => 'en_cours', 'libelle' => 'Suivi en cours']);
        SanteIntervention::factory()->create(['statut' => 'gueri', 'libelle' => 'Suivi termine']);

        Livewire::test(SanteIndex::class)
            ->set('statutFiltre', 'en_cours')
            ->assertSee('Suivi en cours')
            ->assertDontSee('Suivi termine');
    }
}
