<?php

namespace App\Observers;

use App\Models\Parametre;
use Illuminate\Support\Facades\Storage;

class ParametreObserver
{
    /**
     * Supprime l'ancien logo du disque quand il est remplacé par un nouveau,
     * pour éviter d'accumuler des fichiers orphelins.
     */
    public function updating(Parametre $parametre): void
    {
        if ($parametre->isDirty('logo_path') && $parametre->getOriginal('logo_path')) {
            Storage::disk('public')->delete($parametre->getOriginal('logo_path'));
        }
    }
}
