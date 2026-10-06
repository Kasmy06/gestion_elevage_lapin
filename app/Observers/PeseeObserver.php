<?php

namespace App\Observers;

use App\Models\Pesee;

class PeseeObserver
{
    public function created(Pesee $pesee): void
    {
        $this->synchroniserPoidsLapin($pesee);
    }

    public function updated(Pesee $pesee): void
    {
        $this->synchroniserPoidsLapin($pesee);
    }

    public function deleted(Pesee $pesee): void
    {
        $this->synchroniserPoidsLapin($pesee);
    }

    /**
     * Le lapin garde en cache le poids de sa pesée la plus récente
     * (par date_pesee, puis par id) pour un affichage rapide dans les listes.
     */
    private function synchroniserPoidsLapin(Pesee $pesee): void
    {
        $lapin = $pesee->lapin;

        if (! $lapin) {
            return;
        }

        $derniere = $lapin->pesees()->reorder()->orderByDesc('date_pesee')->orderByDesc('id')->first();

        $lapin->update(['poids_actuel_g' => $derniere?->poids_g]);
    }
}
