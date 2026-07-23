<?php

namespace App\Livewire\Cages;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Cage;
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

    public function restaurer(int $cageId): void
    {
        $cage = Cage::onlyTrashed()->findOrFail($cageId);
        $cage->restore();

        $this->flash('Le clapier '.$cage->numero.' a été restauré.');
    }

    public function supprimerDefinitivement(int $cageId): void
    {
        $cage = Cage::onlyTrashed()->findOrFail($cageId);
        $numero = $cage->numero;
        $cage->forceDelete();

        $this->flash('Le clapier '.$numero.' a été supprimé définitivement.', 'error');
    }

    public function render()
    {
        $cages = Cage::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.cages.corbeille', [
            'cages' => $cages,
        ]);
    }
}
