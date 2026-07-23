<?php

namespace Tests\Feature;

use App\Livewire\Clients\Corbeille as ClientsCorbeille;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Comptabilite\Corbeille as DepensesCorbeille;
use App\Livewire\Comptabilite\Index as ComptabiliteIndex;
use App\Livewire\Employes\Corbeille as EmployesCorbeille;
use App\Livewire\Employes\Index as EmployesIndex;
use App\Livewire\Parametres\Index as ParametresIndex;
use App\Livewire\Rapports\Index as RapportsIndex;
use App\Livewire\Utilisateurs\Index as UtilisateursIndex;
use App\Livewire\Ventes\Corbeille as VentesCorbeille;
use App\Livewire\Ventes\Index as VentesIndex;
use App\Models\Client;
use App\Models\Depense;
use App\Models\Employe;
use App\Models\Parametre;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NouveauxModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_administrateur_peut_creer_un_employe(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(EmployesIndex::class)
            ->call('creer')
            ->set('nom', 'Kouadio Yao')
            ->set('poste', 'Gardien')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('employes', ['nom' => 'Kouadio Yao', 'poste' => 'Gardien']);
    }

    public function test_un_utilisateur_peut_creer_un_client(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ClientsIndex::class)
            ->call('creer')
            ->set('nom', 'Marché Central')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', ['nom' => 'Marché Central']);
    }

    public function test_le_montant_total_dune_vente_est_calcule_automatiquement(): void
    {
        $this->actingAs(User::factory()->create());
        $client = Client::factory()->create();

        Livewire::test(VentesIndex::class)
            ->call('creer')
            ->set('client_id', $client->id)
            ->set('description', 'Lapin adulte')
            ->set('quantite', 3)
            ->set('prix_unitaire', 2500)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ventes', [
            'client_id' => $client->id,
            'quantite' => 3,
            'prix_unitaire' => 2500,
            'montant_total' => 7500,
        ]);
    }

    public function test_la_comptabilite_calcule_le_profit_du_mois(): void
    {
        $this->actingAs(User::factory()->create());

        Vente::factory()->create(['date' => Carbon::today(), 'quantite' => 1, 'prix_unitaire' => 10000, 'montant_total' => 10000]);
        Depense::factory()->create(['date' => Carbon::today(), 'montant' => 4000]);

        Livewire::test(ComptabiliteIndex::class)
            ->assertViewHas('recettes', 10000.0)
            ->assertViewHas('depenses', 4000.0)
            ->assertViewHas('profit', 6000.0);
    }

    public function test_la_liste_des_depenses_du_mois_saffiche_et_se_pagine(): void
    {
        $this->actingAs(User::factory()->create());

        Depense::factory()->count(20)->create(['date' => Carbon::today(), 'libelle' => 'Dépense du mois']);
        Depense::factory()->create(['date' => Carbon::today()->subMonth(), 'libelle' => 'Dépense hors période']);

        $component = Livewire::test(ComptabiliteIndex::class);

        $component->assertSee('Dépense du mois')
            ->assertDontSee('Dépense hors période');

        $this->assertSame(20, $component->viewData('depensesPeriode')->total());
    }

    public function test_les_depenses_du_mois_sont_exportables_en_csv(): void
    {
        $this->actingAs(User::factory()->create());

        Depense::factory()->create(['date' => Carbon::today(), 'libelle' => 'Vaccin VHD', 'montant' => 1500]);
        Depense::factory()->create(['date' => Carbon::today()->subMonth(), 'libelle' => 'Hors période']);

        Livewire::test(ComptabiliteIndex::class)
            ->call('exporterDepenses')
            ->assertFileDownloaded('depenses-'.Carbon::today()->format('Y-m').'.csv');
    }

    public function test_une_depense_peut_etre_ajoutee_depuis_la_comptabilite(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ComptabiliteIndex::class)
            ->call('ouvrirDepense')
            ->set('categorie', 'sante')
            ->set('libelle', 'Vaccin VHD')
            ->set('montant', 1500)
            ->set('date', Carbon::today()->toDateString())
            ->call('enregistrerDepense')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('depenses', ['libelle' => 'Vaccin VHD', 'montant' => 1500]);
    }

    public function test_lexport_csv_du_rapport_cheptel_est_telechargeable(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(RapportsIndex::class)
            ->call('exporterCheptel')
            ->assertFileDownloaded('rapport-cheptel.csv');
    }

    public function test_les_parametres_peuvent_etre_mis_a_jour_et_sont_repercutes(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(ParametresIndex::class)
            ->set('nom_ferme', 'Ferme Amani')
            ->set('devise', 'XOF')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('XOF', Parametre::current()->devise);
    }

    public function test_seul_un_administrateur_peut_acceder_aux_parametres(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'eleveur']));

        Livewire::test(ParametresIndex::class)
            ->assertStatus(403);
    }

    public function test_un_administrateur_peut_changer_le_logo_de_la_ferme(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(ParametresIndex::class)
            ->set('logo', UploadedFile::fake()->image('logo.png'))
            ->call('save')
            ->assertHasNoErrors();

        $logoPath = Parametre::current()->logo_path;

        $this->assertNotNull($logoPath);
        Storage::disk('public')->assertExists($logoPath);
    }

    public function test_remplacer_le_logo_supprime_lancien_fichier(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(ParametresIndex::class)
            ->set('logo', UploadedFile::fake()->image('ancien.png'))
            ->call('save');

        $ancienLogoPath = Parametre::current()->logo_path;

        Livewire::test(ParametresIndex::class)
            ->set('logo', UploadedFile::fake()->image('nouveau.png'))
            ->call('save');

        Storage::disk('public')->assertMissing($ancienLogoPath);
        Storage::disk('public')->assertExists(Parametre::current()->logo_path);
    }

    public function test_le_dernier_administrateur_ne_peut_pas_etre_rétrogradé(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(UtilisateursIndex::class)
            ->call('modifier', $admin->id)
            ->set('role', 'eleveur')
            ->call('save');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_seul_un_administrateur_peut_supprimer_un_client(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $client = Client::factory()->create();

        $this->actingAs($eleveur);

        Livewire::test(ClientsIndex::class)
            ->call('supprimer', $client->id)
            ->assertStatus(403);
    }

    public function test_un_client_supprime_apparait_dans_la_corbeille_et_peut_etre_restaure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create(['nom' => 'Marché Test']);

        $this->actingAs($admin);

        Livewire::test(ClientsIndex::class)->call('supprimer', $client->id);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);

        Livewire::test(ClientsCorbeille::class)
            ->assertSee('Marché Test')
            ->call('restaurer', $client->id);

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'deleted_at' => null]);
    }

    public function test_seul_un_administrateur_peut_supprimer_une_vente(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $vente = Vente::factory()->create();

        $this->actingAs($eleveur);

        Livewire::test(VentesIndex::class)
            ->call('supprimer', $vente->id)
            ->assertStatus(403);
    }

    public function test_une_vente_supprimee_apparait_dans_la_corbeille_et_peut_etre_restauree(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vente = Vente::factory()->create(['description' => 'Vente test']);

        $this->actingAs($admin);

        Livewire::test(VentesIndex::class)->call('supprimer', $vente->id);

        $this->assertSoftDeleted('ventes', ['id' => $vente->id]);

        Livewire::test(VentesCorbeille::class)
            ->assertSee('Vente test')
            ->call('restaurer', $vente->id);

        $this->assertDatabaseHas('ventes', ['id' => $vente->id, 'deleted_at' => null]);
    }

    public function test_seul_un_administrateur_peut_supprimer_une_depense(): void
    {
        $eleveur = User::factory()->create(['role' => 'eleveur']);
        $depense = Depense::factory()->create();

        $this->actingAs($eleveur);

        Livewire::test(ComptabiliteIndex::class)
            ->call('supprimerDepense', $depense->id)
            ->assertStatus(403);
    }

    public function test_une_depense_supprimee_apparait_dans_la_corbeille_et_peut_etre_restauree(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $depense = Depense::factory()->create(['libelle' => 'Dépense test']);

        $this->actingAs($admin);

        Livewire::test(ComptabiliteIndex::class)->call('supprimerDepense', $depense->id);

        $this->assertSoftDeleted('depenses', ['id' => $depense->id]);

        Livewire::test(DepensesCorbeille::class)
            ->assertSee('Dépense test')
            ->call('restaurer', $depense->id);

        $this->assertDatabaseHas('depenses', ['id' => $depense->id, 'deleted_at' => null]);
    }

    public function test_un_employe_supprime_apparait_dans_la_corbeille_et_peut_etre_restaure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employe = Employe::factory()->create(['nom' => 'Employe Test']);

        $this->actingAs($admin);

        Livewire::test(EmployesIndex::class)->call('supprimer', $employe->id);

        $this->assertSoftDeleted('employes', ['id' => $employe->id]);

        Livewire::test(EmployesCorbeille::class)
            ->assertSee('Employe Test')
            ->call('restaurer', $employe->id);

        $this->assertDatabaseHas('employes', ['id' => $employe->id, 'deleted_at' => null]);
    }
}
