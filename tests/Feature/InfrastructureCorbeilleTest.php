<?php

namespace Tests\Feature;

use App\Livewire\Alimentation\Aliments\Corbeille as AlimentsCorbeille;
use App\Livewire\Alimentation\Aliments\Index as AlimentsIndex;
use App\Livewire\Cages\Corbeille as CagesCorbeille;
use App\Livewire\Cages\Index as CagesIndex;
use App\Livewire\Races\Corbeille as RacesCorbeille;
use App\Livewire\Races\Index as RacesIndex;
use App\Models\Aliment;
use App\Models\Cage;
use App\Models\Race;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InfrastructureCorbeilleTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_clapier_supprime_apparait_dans_la_corbeille_et_peut_etre_restaure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cage = Cage::factory()->create(['numero' => 'C-TEST']);

        $this->actingAs($admin);

        Livewire::test(CagesIndex::class)->call('supprimer', $cage->id);

        $this->assertSoftDeleted('cages', ['id' => $cage->id]);

        Livewire::test(CagesCorbeille::class)
            ->assertSee('C-TEST')
            ->call('restaurer', $cage->id);

        $this->assertDatabaseHas('cages', ['id' => $cage->id, 'deleted_at' => null]);
    }

    public function test_un_clapier_peut_etre_supprime_definitivement_depuis_la_corbeille(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cage = Cage::factory()->create();
        $cage->delete();

        $this->actingAs($admin);

        Livewire::test(CagesCorbeille::class)->call('supprimerDefinitivement', $cage->id);

        $this->assertDatabaseMissing('cages', ['id' => $cage->id]);
    }

    public function test_une_race_supprimee_apparait_dans_la_corbeille_et_peut_etre_restauree(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $race = Race::factory()->create(['nom' => 'Race Test']);

        $this->actingAs($admin);

        Livewire::test(RacesIndex::class)->call('supprimer', $race->id);

        $this->assertSoftDeleted('races', ['id' => $race->id]);

        Livewire::test(RacesCorbeille::class)
            ->assertSee('Race Test')
            ->call('restaurer', $race->id);

        $this->assertDatabaseHas('races', ['id' => $race->id, 'deleted_at' => null]);
    }

    public function test_un_aliment_supprime_apparait_dans_la_corbeille_et_peut_etre_restaure(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $aliment = Aliment::factory()->create(['nom' => 'Aliment Test']);

        $this->actingAs($admin);

        Livewire::test(AlimentsIndex::class)->call('supprimer', $aliment->id);

        $this->assertSoftDeleted('aliments', ['id' => $aliment->id]);

        Livewire::test(AlimentsCorbeille::class)
            ->assertSee('Aliment Test')
            ->call('restaurer', $aliment->id);

        $this->assertDatabaseHas('aliments', ['id' => $aliment->id, 'deleted_at' => null]);
    }
}
