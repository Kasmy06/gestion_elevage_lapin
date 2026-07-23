<?php

namespace App\Livewire\Lapins;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Lapin;
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

    public function restaurer(int $lapinId): void
    {
        $lapin = Lapin::onlyTrashed()->findOrFail($lapinId);
        $lapin->restore();

        $this->flash('Le lapin '.$lapin->identifiant.' a été restauré.');
    }

    public function supprimerDefinitivement(int $lapinId): void
    {
        $lapin = Lapin::onlyTrashed()->findOrFail($lapinId);
        $identifiant = $lapin->identifiant;
        $lapin->forceDelete();

        $this->flash('Le lapin '.$identifiant.' a été supprimé définitivement.', 'error');
    }

    public function render()
    {
        $lapins = Lapin::onlyTrashed()
            ->with('race')
            ->withCount(['pesees', 'santeInterventions', 'sorties'])
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.lapins.corbeille', [
            'lapins' => $lapins,
        ]);
    }
}
