<?php

namespace App\Observers;

use App\Models\MouvementAliment;

class MouvementAlimentObserver
{
    /**
     * Chaque mouvement (entrée en stock ou distribution) ajuste le stock
     * courant de l'aliment concerné. Le journal est en ajout seul (append-only),
     * sans édition/suppression exposée dans l'interface.
     */
    public function created(MouvementAliment $mouvementAliment): void
    {
        $delta = $mouvementAliment->type_mouvement === 'entree'
            ? $mouvementAliment->quantite
            : -$mouvementAliment->quantite;

        $mouvementAliment->aliment?->increment('stock_actuel', $delta);
    }
}
