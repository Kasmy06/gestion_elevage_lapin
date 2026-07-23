<?php

namespace App\Observers;

use App\Models\Lapin;
use Illuminate\Support\Facades\Storage;

class LapinObserver
{
    /**
     * Supprime l'ancienne photo du disque quand elle est remplacée par une nouvelle,
     * pour éviter d'accumuler des fichiers orphelins au fil des années.
     */
    public function updating(Lapin $lapin): void
    {
        if ($lapin->isDirty('photo_path') && $lapin->getOriginal('photo_path')) {
            Storage::disk('public')->delete($lapin->getOriginal('photo_path'));
        }
    }

    /**
     * La suppression définitive (corbeille) efface aussi la photo associée.
     * La suppression douce ne touche pas au fichier : le lapin peut être restauré.
     */
    public function forceDeleted(Lapin $lapin): void
    {
        if ($lapin->photo_path) {
            Storage::disk('public')->delete($lapin->photo_path);
        }
    }
}
