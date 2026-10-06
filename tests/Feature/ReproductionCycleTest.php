<?php

namespace Tests\Feature;

use App\Livewire\Reproduction\MisesBas\Index as MisesBasIndex;
use App\Livewire\Reproduction\Saillies\Index as SaillesIndex;
use App\Models\Lapin;
use App\Models\Saillie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class ReproductionCycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_cycle_complet_saillie_diagnostic_mise_bas_sevrage_et_generation_des_lapereaux(): void
    {
        $user = User::factory()->create();
        $male = Lapin::factory()->reproducteur('male')->create(['identifiant' => 'M-1']);
        $femelle = Lapin::factory()->reproducteur('femelle')->create(['identifiant' => 'F-1']);

        $this->actingAs($user);

        // 1. Saillie : la date de mise bas prévue est calculée automatiquement (+31 jours).
        $dateSaillie = Carbon::today()->subDays(40)->toDateString();

        Livewire::test(SaillesIndex::class)
            ->call('ouvrirCreation')
            ->set('male_id', $male->id)
            ->set('femelle_id', $femelle->id)
            ->set('date_saillie', $dateSaillie)
            ->call('enregistrerSaillie')
            ->assertHasNoErrors();

        $saillie = Saillie::firstOrFail();
        $this->assertEquals(
            Carbon::parse($dateSaillie)->addDays(Saillie::JOURS_GESTATION)->toDateString(),
            $saillie->date_mise_bas_prevue->toDateString()
        );

        // 2. Diagnostic positif.
        Livewire::test(SaillesIndex::class)
            ->call('ouvrirDiagnostic', $saillie->id)
            ->set('diagnostic_gestation', 'positif')
            ->set('date_diagnostic', Carbon::today()->subDays(30)->toDateString())
            ->call('enregistrerDiagnostic')
            ->assertHasNoErrors();

        $this->assertSame('positif', $saillie->fresh()->diagnostic_gestation);

        // 3. Mise bas : la date de sevrage prévue est calculée automatiquement (+35 jours).
        $dateMiseBas = Carbon::today()->subDays(9)->toDateString();

        Livewire::test(SaillesIndex::class)
            ->call('ouvrirMiseBas', $saillie->id)
            ->set('date_mise_bas', $dateMiseBas)
            ->set('nb_nes_vivants', 6)
            ->set('nb_morts_nes', 1)
            ->call('enregistrerMiseBas')
            ->assertHasNoErrors();

        $miseBas = $saillie->fresh()->miseBas;
        $this->assertNotNull($miseBas);
        $this->assertSame(6, $miseBas->nb_nes_vivants);
        $this->assertEquals(
            Carbon::parse($dateMiseBas)->addDays(\App\Models\MiseBas::JOURS_AVANT_SEVRAGE)->toDateString(),
            $miseBas->date_sevrage_prevue->toDateString()
        );

        // 4. Sevrage.
        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirSevrage', $miseBas->id)
            ->set('date_sevrage', Carbon::today()->toDateString())
            ->set('nb_sevres', 5)
            ->call('enregistrerSevrage')
            ->assertHasNoErrors();

        $sevrage = $miseBas->fresh()->sevrage;
        $this->assertNotNull($sevrage);
        $this->assertSame(5, $sevrage->nb_sevres);

        // 5. Génération des fiches lapereaux : 5 nouvelles fiches avec la bonne généalogie.
        $lapinsAvant = Lapin::count();

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirGenerationLapereaux', $miseBas->id)
            ->call('genererLapereaux');

        $this->assertSame($lapinsAvant + 5, Lapin::count());
        $this->assertSame(5, Lapin::where('mere_id', $femelle->id)->where('pere_id', $male->id)->count());
        $this->assertTrue($sevrage->fresh()->lapereaux_generes);

        // La génération est idempotente : un second appel ne recrée pas de fiches.
        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirGenerationLapereaux', $miseBas->id)
            ->call('genererLapereaux');

        $this->assertSame($lapinsAvant + 5, Lapin::count());
    }

    public function test_une_mise_bas_peut_etre_modifiee(): void
    {
        $user = User::factory()->create();
        $femelle = Lapin::factory()->reproducteur('femelle')->create(['identifiant' => 'F-2']);
        $saillie = Saillie::factory()->create(['femelle_id' => $femelle->id, 'diagnostic_gestation' => 'positif']);
        $miseBas = \App\Models\MiseBas::factory()->create([
            'saillie_id' => $saillie->id,
            'femelle_id' => $femelle->id,
            'date_mise_bas' => Carbon::today()->subDays(5),
            'nb_nes_vivants' => 6,
            'nb_morts_nes' => 1,
        ]);

        $this->actingAs($user);

        $nouvelleDate = Carbon::today()->subDays(4)->toDateString();

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirModification', $miseBas->id)
            ->set('date_mise_bas', $nouvelleDate)
            ->set('nb_nes_vivants', 7)
            ->set('nb_morts_nes', 0)
            ->set('notes', 'Portée nombreuse')
            ->call('modifierMiseBas')
            ->assertHasNoErrors();

        $miseBas->refresh();
        $this->assertSame($nouvelleDate, $miseBas->date_mise_bas->toDateString());
        $this->assertSame(7, $miseBas->nb_nes_vivants);
        $this->assertSame(0, $miseBas->nb_morts_nes);
        $this->assertSame('Portée nombreuse', $miseBas->notes);
    }

    public function test_un_deces_apres_naissance_reduit_le_nombre_propose_au_sevrage(): void
    {
        $femelle = Lapin::factory()->reproducteur('femelle')->create();
        $saillie = Saillie::factory()->create(['femelle_id' => $femelle->id, 'diagnostic_gestation' => 'positif']);
        $miseBas = \App\Models\MiseBas::factory()->create([
            'saillie_id' => $saillie->id,
            'femelle_id' => $femelle->id,
            'nb_nes_vivants' => 6,
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirMortalite', $miseBas->id)
            ->set('mortaliteStade', 'naissance')
            ->set('mortaliteNombre', 2)
            ->call('enregistrerMortalite')
            ->assertHasNoErrors();

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirSevrage', $miseBas->id)
            ->assertSet('nb_sevres', 4);

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirMortalite', $miseBas->id)
            ->set('mortaliteStade', 'naissance')
            ->set('mortaliteNombre', 5)
            ->call('enregistrerMortalite')
            ->assertHasErrors('mortaliteNombre');
    }

    public function test_un_deces_apres_sevrage_reduit_les_fiches_lapereaux_generees(): void
    {
        $femelle = Lapin::factory()->reproducteur('femelle')->create();
        $male = Lapin::factory()->reproducteur('male')->create();
        $saillie = Saillie::factory()->create(['femelle_id' => $femelle->id, 'male_id' => $male->id, 'diagnostic_gestation' => 'positif']);
        $miseBas = \App\Models\MiseBas::factory()->create([
            'saillie_id' => $saillie->id,
            'femelle_id' => $femelle->id,
            'nb_nes_vivants' => 6,
        ]);
        $miseBas->sevrage()->create(['date_sevrage' => Carbon::today(), 'nb_sevres' => 5]);

        $this->actingAs(User::factory()->create());

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirMortalite', $miseBas->id)
            ->set('mortaliteStade', 'sevrage')
            ->set('mortaliteNombre', 2)
            ->call('enregistrerMortalite')
            ->assertHasNoErrors();

        $lapinsAvant = Lapin::count();

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirGenerationLapereaux', $miseBas->id)
            ->call('genererLapereaux');

        $this->assertSame($lapinsAvant + 3, Lapin::count());
    }

    public function test_un_deces_apres_sevrage_est_refuse_une_fois_les_fiches_creees(): void
    {
        $femelle = Lapin::factory()->reproducteur('femelle')->create();
        $saillie = Saillie::factory()->create(['femelle_id' => $femelle->id, 'diagnostic_gestation' => 'positif']);
        $miseBas = \App\Models\MiseBas::factory()->create([
            'saillie_id' => $saillie->id,
            'femelle_id' => $femelle->id,
            'nb_nes_vivants' => 6,
        ]);
        $miseBas->sevrage()->create([
            'date_sevrage' => Carbon::today(),
            'nb_sevres' => 5,
            'lapereaux_generes' => true,
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirMortalite', $miseBas->id)
            ->set('mortaliteStade', 'sevrage')
            ->set('mortaliteNombre', 1)
            ->call('enregistrerMortalite')
            ->assertHasErrors('mortaliteStade');

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirMortalite', $miseBas->id)
            ->set('mortaliteStade', 'naissance')
            ->set('mortaliteNombre', 1)
            ->call('enregistrerMortalite')
            ->assertHasErrors('mortaliteStade');

        $this->assertSame(0, $miseBas->mortalites()->count());
    }

    public function test_le_nombre_de_nes_vivants_ne_peut_pas_devenir_inferieur_au_nombre_deja_sevre(): void
    {
        $user = User::factory()->create();
        $femelle = Lapin::factory()->reproducteur('femelle')->create();
        $saillie = Saillie::factory()->create(['femelle_id' => $femelle->id, 'diagnostic_gestation' => 'positif']);
        $miseBas = \App\Models\MiseBas::factory()->create([
            'saillie_id' => $saillie->id,
            'femelle_id' => $femelle->id,
            'date_mise_bas' => Carbon::today()->subDays(5),
            'nb_nes_vivants' => 6,
        ]);
        $miseBas->sevrage()->create([
            'date_sevrage' => Carbon::today(),
            'nb_sevres' => 5,
        ]);

        $this->actingAs($user);

        Livewire::test(MisesBasIndex::class)
            ->call('ouvrirModification', $miseBas->id)
            ->set('nb_nes_vivants', 3)
            ->call('modifierMiseBas')
            ->assertHasErrors('nb_nes_vivants');

        $this->assertSame(6, $miseBas->fresh()->nb_nes_vivants);
    }
}
