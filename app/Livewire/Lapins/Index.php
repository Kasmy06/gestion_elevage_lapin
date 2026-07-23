<?php

namespace App\Livewire\Lapins;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Lapin;
use App\Models\Race;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $sexe = '';

    #[Url(history: true)]
    public string $statut = '';

    #[Url(history: true)]
    public string $raceId = '';

    public function updating($property): void
    {
        if (in_array($property, ['search', 'sexe', 'statut', 'raceId'], true)) {
            $this->resetPage();
        }
    }

    public function supprimer(Lapin $lapin): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $lapin->delete();

        $this->flash('Le lapin '.$lapin->identifiant.' a été supprimé.');
    }

    public function render()
    {
        $lapins = Lapin::query()
            ->with(['race', 'cage'])
            ->when($this->search, fn ($query) => $query->where('identifiant', 'like', "%{$this->search}%"))
            ->when($this->sexe, fn ($query) => $query->where('sexe', $this->sexe))
            ->when($this->statut, fn ($query) => $query->where('statut', $this->statut))
            ->when($this->raceId, fn ($query) => $query->where('race_id', $this->raceId))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.lapins.index', [
            'lapins' => $lapins,
            'races' => Race::orderBy('nom')->get(),
        ]);
    }
}
