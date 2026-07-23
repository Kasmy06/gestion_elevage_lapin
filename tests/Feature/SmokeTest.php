<?php

namespace Tests\Feature;

use App\Models\Aliment;
use App\Models\Cage;
use App\Models\Lapin;
use App\Models\Race;
use App\Models\SanteIntervention;
use App\Models\Sortie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_toutes_les_pages_principales_se_chargent_pour_un_eleveur(): void
    {
        $user = User::factory()->create(['role' => 'eleveur']);
        $race = Race::factory()->create();
        $cage = Cage::factory()->create();
        $lapin = Lapin::factory()->create(['race_id' => $race->id, 'cage_id' => $cage->id]);
        Aliment::factory()->create();
        SanteIntervention::factory()->create(['lapin_id' => $lapin->id]);
        Sortie::factory()->create(['lapin_id' => $lapin->id]);

        $this->actingAs($user);

        $routes = [
            'dashboard',
            'lapins.index',
            'lapins.create',
            'cages.index',
            'races.index',
            'reproduction.saillies.index',
            'reproduction.mises-bas.index',
            'sante.index',
            'alimentation.aliments.index',
            'alimentation.mouvements.index',
            'sorties.index',
            'clients.index',
            'ventes.index',
            'comptabilite.index',
            'rapports.index',
            'profile',
        ];

        foreach ($routes as $routeName) {
            $this->get(route($routeName))->assertOk();
        }

        $this->get(route('lapins.show', $lapin))->assertOk();
        $this->get(route('lapins.edit', $lapin))->assertOk();
    }

    public function test_la_page_parametres_est_reservee_aux_administrateurs(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($eleveur)->get(route('parametres.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('parametres.index'))->assertOk();
    }

    public function test_la_page_utilisateurs_est_reservee_aux_administrateurs(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($eleveur)->get(route('utilisateurs.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('utilisateurs.index'))->assertOk();
    }

    public function test_la_page_employes_est_reservee_aux_administrateurs(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($eleveur)->get(route('employes.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('employes.index'))->assertOk();
    }

    public function test_la_corbeille_des_lapins_est_reservee_aux_administrateurs(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($eleveur)->get(route('lapins.corbeille'))->assertForbidden();
        $this->actingAs($admin)->get(route('lapins.corbeille'))->assertOk();
    }

    public function test_les_corbeilles_clients_ventes_et_depenses_sont_reservees_aux_administrateurs(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['clients.corbeille', 'ventes.corbeille', 'comptabilite.depenses.corbeille', 'employes.corbeille'] as $routeName) {
            $this->actingAs($eleveur)->get(route($routeName))->assertForbidden();
            $this->actingAs($admin)->get(route($routeName))->assertOk();
        }
    }

    public function test_les_corbeilles_clapiers_races_et_aliments_sont_reservees_aux_administrateurs(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['cages.corbeille', 'races.corbeille', 'alimentation.aliments.corbeille'] as $routeName) {
            $this->actingAs($eleveur)->get(route($routeName))->assertForbidden();
            $this->actingAs($admin)->get(route($routeName))->assertOk();
        }
    }

    public function test_seul_un_administrateur_peut_supprimer_une_race(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $race = Race::factory()->create();

        $this->actingAs($eleveur);

        \Livewire\Livewire::test(\App\Livewire\Races\Index::class)
            ->call('supprimer', $race->id)
            ->assertStatus(403);
    }
}
