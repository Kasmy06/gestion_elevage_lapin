<?php

namespace App\Livewire\Races;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Race;
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

    public function restaurer(int $raceId): void
    {
        $race = Race::onlyTrashed()->findOrFail($raceId);
        $race->restore();

        $this->flash('La race « '.$race->nom.' » a été restaurée.');
    }

    public function supprimerDefinitivement(int $raceId): void
    {
        $race = Race::onlyTrashed()->findOrFail($raceId);
        $nom = $race->nom;
        $race->forceDelete();

        $this->flash('La race « '.$nom.' » a été supprimée définitivement.', 'error');
    }

    public function render()
    {
        $races = Race::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.races.corbeille', [
            'races' => $races,
        ]);
    }
}
