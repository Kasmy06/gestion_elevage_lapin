<?php

namespace App\Livewire\Comptabilite;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Depense;
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

    public function restaurer(int $depenseId): void
    {
        $depense = Depense::onlyTrashed()->findOrFail($depenseId);
        $depense->restore();

        $this->flash('La dépense « '.$depense->libelle.' » a été restaurée.');
    }

    public function supprimerDefinitivement(int $depenseId): void
    {
        $depense = Depense::onlyTrashed()->findOrFail($depenseId);
        $libelle = $depense->libelle;
        $depense->forceDelete();

        $this->flash('La dépense « '.$libelle.' » a été supprimée définitivement.', 'error');
    }

    public function render()
    {
        $depenses = Depense::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.comptabilite.corbeille', [
            'depenses' => $depenses,
        ]);
    }
}
