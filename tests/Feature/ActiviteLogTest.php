<?php

namespace Tests\Feature;

use App\Livewire\Activites\Index as ActivitesIndex;
use App\Models\ActivityLog;
use App\Models\Lapin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActiviteLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_creation_dun_lapin_est_journalisee(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $lapin = Lapin::factory()->create(['identifiant' => 'L-JOURNAL']);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'action' => 'created',
            'subject_type' => Lapin::class,
            'subject_id' => $lapin->id,
            'subject_label' => 'L-JOURNAL',
        ]);
    }

    public function test_le_nom_de_lauteur_reste_affiche_meme_apres_suppression_de_son_compte(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $auteur = User::factory()->create(['name' => 'Ancien Employé']);

        $this->actingAs($auteur);
        $lapin = Lapin::factory()->create(['identifiant' => 'L-AUTEUR-SUPPRIME']);

        $this->actingAs($admin);
        $auteur->delete();

        $log = ActivityLog::where('subject_type', Lapin::class)
            ->where('subject_id', $lapin->id)
            ->where('action', 'created')
            ->first();

        $this->assertNull($log->user_id);
        $this->assertSame('Ancien Employé', $log->user_name);

        Livewire::test(ActivitesIndex::class)->assertSee('Ancien Employé');
    }

    public function test_la_modification_dun_lapin_est_journalisee_avec_le_detail_des_changements(): void
    {
        $user = User::factory()->create();
        $lapin = Lapin::factory()->create(['statut' => 'jeune']);

        $this->actingAs($user);
        $lapin->update(['statut' => 'reproducteur']);

        $log = ActivityLog::where('subject_type', Lapin::class)
            ->where('subject_id', $lapin->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('jeune', $log->changes['statut']['avant']);
        $this->assertSame('reproducteur', $log->changes['statut']['apres']);
    }

    public function test_la_suppression_dun_lapin_est_journalisee(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lapin = Lapin::factory()->create();

        $this->actingAs($admin);
        $lapin->delete();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted',
            'subject_type' => Lapin::class,
            'subject_id' => $lapin->id,
        ]);
    }

    public function test_la_restauration_et_la_suppression_definitive_sont_journalisees_separement(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lapin = Lapin::factory()->create();
        $lapin->delete();

        $this->actingAs($admin);
        $lapin->restore();
        $lapin->forceDelete();

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Lapin::class,
            'subject_id' => $lapin->id,
            'action' => 'restored',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Lapin::class,
            'subject_id' => $lapin->id,
            'action' => 'force_deleted',
        ]);

        // La suppression définitive ne doit pas générer un doublon "deleted"
        // (un seul, provenant du delete() initial avant restauration).
        $this->assertSame(
            1,
            ActivityLog::where('subject_type', Lapin::class)
                ->where('subject_id', $lapin->id)
                ->where('action', 'deleted')
                ->count()
        );
    }

    public function test_le_changement_de_mot_de_passe_est_journalise_sans_exposer_la_valeur(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $utilisateur = User::factory()->create();
        $utilisateur->update(['password' => bcrypt('nouveau-mot-de-passe')]);

        $log = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $utilisateur->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('••••••', $log->changes['password']['apres']);
        $this->assertStringNotContainsString('nouveau-mot-de-passe', json_encode($log->changes));
    }

    public function test_le_journal_dactivite_est_reserve_aux_administrateurs(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($eleveur)->get(route('activites.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('activites.index'))->assertOk();
    }

    public function test_le_journal_peut_etre_filtre_par_module_et_par_action(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $lapin = Lapin::factory()->create(['identifiant' => 'L-FILTRE']);
        $lapin->update(['statut' => 'reproducteur']);

        Livewire::test(ActivitesIndex::class)
            ->set('module', Lapin::class)
            ->set('action', 'created')
            ->assertSee('L-FILTRE')
            ->set('action', 'updated')
            ->assertSee('L-FILTRE');
    }
}
