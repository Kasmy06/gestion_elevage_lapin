<?php

namespace Tests\Feature;

use App\Livewire\Lapins\Corbeille;
use App\Livewire\Lapins\Form;
use App\Livewire\Lapins\Index;
use App\Models\Lapin;
use App\Models\Race;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LapinManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_authentifie_peut_creer_un_lapin(): void
    {
        $user = User::factory()->create();
        $race = Race::factory()->create();

        $this->actingAs($user);

        Livewire::test(Form::class)
            ->set('identifiant', 'L-001')
            ->set('race_id', $race->id)
            ->set('sexe', 'femelle')
            ->set('statut', 'jeune')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('lapins', [
            'identifiant' => 'L-001',
            'race_id' => $race->id,
            'sexe' => 'femelle',
        ]);
    }

    public function test_lidentifiant_doit_etre_unique(): void
    {
        $user = User::factory()->create();
        Lapin::factory()->create(['identifiant' => 'L-001']);

        $this->actingAs($user);

        Livewire::test(Form::class)
            ->set('identifiant', 'L-001')
            ->set('statut', 'jeune')
            ->call('save')
            ->assertHasErrors(['identifiant' => 'unique']);
    }

    public function test_la_liste_des_lapins_peut_etre_filtree_par_statut(): void
    {
        $user = User::factory()->create();
        Lapin::factory()->create(['identifiant' => 'L-JEUNE', 'statut' => 'jeune']);
        Lapin::factory()->create(['identifiant' => 'L-REPRO', 'statut' => 'reproducteur']);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('statut', 'reproducteur')
            ->assertSee('L-REPRO')
            ->assertDontSee('L-JEUNE');
    }

    public function test_un_visiteur_non_authentifie_est_redirige_vers_la_connexion(): void
    {
        $this->get(route('lapins.index'))->assertRedirect(route('login'));
    }

    public function test_seul_un_administrateur_peut_supprimer_un_lapin(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $lapin = Lapin::factory()->create();

        $this->actingAs($eleveur);

        Livewire::test(Index::class)
            ->call('supprimer', $lapin->id)
            ->assertStatus(403);
    }

    public function test_un_lapin_supprime_apparait_dans_la_corbeille_et_peut_etre_restaure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lapin = Lapin::factory()->create(['identifiant' => 'L-CORB']);

        $this->actingAs($admin);

        Livewire::test(Index::class)->call('supprimer', $lapin->id);

        $this->assertSoftDeleted('lapins', ['id' => $lapin->id]);

        Livewire::test(Corbeille::class)
            ->assertSee('L-CORB')
            ->call('restaurer', $lapin->id);

        $this->assertDatabaseHas('lapins', ['id' => $lapin->id, 'deleted_at' => null]);
    }

    public function test_un_eleveur_ne_peut_pas_acceder_a_la_corbeille(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);

        $this->actingAs($eleveur)->get(route('lapins.corbeille'))->assertForbidden();
    }

    public function test_un_lapin_peut_etre_supprime_definitivement_depuis_la_corbeille(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lapin = Lapin::factory()->create();
        $lapin->delete();

        $this->actingAs($admin);

        Livewire::test(Corbeille::class)->call('supprimerDefinitivement', $lapin->id);

        $this->assertDatabaseMissing('lapins', ['id' => $lapin->id]);
    }
}
