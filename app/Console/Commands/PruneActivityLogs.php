<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-activity-logs {--months=24 : Ancienneté en mois au-delà de laquelle une entrée est purgée}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Purge les entrées du journal d'activité plus anciennes que le seuil configuré";

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $mois = (int) $this->option('months');
        $seuil = now()->subMonths($mois);

        $supprimees = ActivityLog::where('created_at', '<', $seuil)->delete();

        $this->info("{$supprimees} entrée(s) du journal d'activité antérieure(s) au {$seuil->format('d/m/Y')} supprimée(s).");

        return self::SUCCESS;
    }
}
