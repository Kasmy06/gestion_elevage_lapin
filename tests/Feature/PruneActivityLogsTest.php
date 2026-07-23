<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Lapin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneActivityLogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_commande_supprime_les_entrees_plus_anciennes_que_le_seuil(): void
    {
        $ancienne = ActivityLog::create([
            'action' => 'created',
            'subject_type' => Lapin::class,
            'subject_id' => 1,
            'subject_label' => 'Ancienne entrée',
        ]);
        $ancienne->forceFill(['created_at' => now()->subMonths(30)])->save();

        $recente = ActivityLog::create([
            'action' => 'created',
            'subject_type' => Lapin::class,
            'subject_id' => 2,
            'subject_label' => 'Entrée récente',
        ]);

        $this->artisan('app:prune-activity-logs', ['--months' => 24])
            ->assertExitCode(0);

        $this->assertDatabaseMissing('activity_logs', ['id' => $ancienne->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $recente->id]);
    }

    public function test_le_seuil_de_purge_est_configurable(): void
    {
        $entree = ActivityLog::create([
            'action' => 'created',
            'subject_type' => Lapin::class,
            'subject_id' => 1,
            'subject_label' => 'Entrée de 10 mois',
        ]);
        $entree->forceFill(['created_at' => now()->subMonths(10)])->save();

        $this->artisan('app:prune-activity-logs', ['--months' => 24])
            ->assertExitCode(0);
        $this->assertDatabaseHas('activity_logs', ['id' => $entree->id]);

        $this->artisan('app:prune-activity-logs', ['--months' => 6])
            ->assertExitCode(0);
        $this->assertDatabaseMissing('activity_logs', ['id' => $entree->id]);
    }
}
