<?php

namespace App\Observers;

use App\Models\Sortie;

class SortieObserver
{
    /**
     * À l'enregistrement d'une sortie (vente, abattage, mortalité, don),
     * le lapin change de statut et libère sa cage (chapitre 10 de l'Agrodok).
     */
    public function created(Sortie $sortie): void
    {
        $sortie->lapin?->update([
            'statut' => Sortie::STATUT_LAPIN[$sortie->type] ?? 'vendu',
            'cage_id' => null,
        ]);
    }
}
