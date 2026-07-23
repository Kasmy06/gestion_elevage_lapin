<?php

namespace App\Livewire\Employes;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Employe;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Corbeille extends Component
{
    use HasFlashMessage, WithPagination;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function restaurer(int $employeId): void
    {
        $employe = Employe::onlyTrashed()->findOrFail($employeId);
        $employe->restore();

        $this->flash('L\'employé '.$employe->nom.' a été restauré.');
    }

    public function supprimerDefinitivement(int $employeId): void
    {
        $employe = Employe::onlyTrashed()->findOrFail($employeId);
        $nom = $employe->nom;
        $employe->forceDelete();

        $this->flash('L\'employé '.$nom.' a été supprimé définitivement.', 'error');
    }

    public function render()
    {
        $employes = Employe::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.employes.corbeille', [
            'employes' => $employes,
        ]);
    }
}
