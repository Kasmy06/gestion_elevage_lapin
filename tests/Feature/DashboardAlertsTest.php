<?php

namespace Tests\Feature;

use App\Livewire\Alimentation\Mouvements\Index as MouvementsIndex;
use App\Livewire\Dashboard;
use App\Models\Aliment;
use App\Models\Saillie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_dashboard_ne_plante_pas_si_un_lapin_dune_saillie_est_dans_la_corbeille(): void
    {
        $user = User::factory()->create();

        $saillie = Saillie::factory()->create([
            'diagnostic_gestation' => 'en_attente',
            'date_saillie' => Carbon::today()->subDays(Saillie::JOURS_AVANT_DIAGNOSTIC + 1),
        ]);

        $saillie->femelle->delete();

        $this->actingAs($user);

        Livewire::test(Dashboard::class)
            ->assertOk()
            ->assertSee('#'.$saillie->femelle_id);
    }

    public function test_une_alerte_de_stock_bas_apparait_puis_disparait(): void
    {
        $user = User::factory()->create();
        $aliment = Aliment::factory()->create([
            'nom' => 'Son de riz',
            'stock_actuel' => 20,
            'seuil_alerte' => 10,
        ]);

        $this->actingAs($user);

        Livewire::test(Dashboard::class)->assertDontSee('Son de riz');

        // Une distribution qui fait passer le stock sous le seuil déclenche l'alerte.
        Livewire::test(MouvementsIndex::class)
            ->call('creer')
            ->set('aliment_id', $aliment->id)
            ->set('type_mouvement', 'distribution')
            ->set('date', Carbon::today()->toDateString())
            ->set('quantite', 15)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(5, $aliment->fresh()->stock_actuel);

        Livewire::test(Dashboard::class)->assertSee('Son de riz');
    }

    public function test_une_distribution_ne_peut_pas_depasser_le_stock_disponible(): void
    {
        $user = User::factory()->create();
        $aliment = Aliment::factory()->create(['stock_actuel' => 5, 'seuil_alerte' => 1]);

        $this->actingAs($user);

        Livewire::test(MouvementsIndex::class)
            ->call('creer')
            ->set('aliment_id', $aliment->id)
            ->set('type_mouvement', 'distribution')
            ->set('date', Carbon::today()->toDateString())
            ->set('quantite', 10)
            ->call('save')
            ->assertHasErrors('quantite');

        $this->assertEquals(5, $aliment->fresh()->stock_actuel);
    }
}
