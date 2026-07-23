<?php

namespace App\Livewire\Races;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Race;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?Race $editing = null;

    public string $nom = '';

    public string $categorie = 'moyenne';

    public ?float $poids_min_kg = null;

    public ?float $poids_max_kg = null;

    public ?string $description = null;

    public function creer(): void
    {
        $this->reset(['editing', 'nom', 'poids_min_kg', 'poids_max_kg', 'description']);
        $this->categorie = 'moyenne';
        $this->showModal = true;
    }

    public function modifier(Race $race): void
    {
        $this->editing = $race;
        $this->nom = $race->nom;
        $this->categorie = $race->categorie;
        $this->poids_min_kg = $race->poids_min_kg;
        $this->poids_max_kg = $race->poids_max_kg;
        $this->description = $race->description;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('races', 'nom')->ignore($this->editing?->id)],
            'categorie' => ['required', Rule::in(array_keys(Race::CATEGORIES))],
            'poids_min_kg' => ['nullable', 'numeric', 'min:0'],
            'poids_max_kg' => ['nullable', 'numeric', 'gte:poids_min_kg'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'nom' => $this->nom,
            'categorie' => $this->categorie,
            'poids_min_kg' => $this->poids_min_kg,
            'poids_max_kg' => $this->poids_max_kg,
            'description' => $this->description,
        ];

        if ($this->editing) {
            $this->editing->update($data);
            $this->flash('Race « '.$this->nom.' » mise à jour.');
        } else {
            Race::create($data);
            $this->flash('Race « '.$this->nom.' » ajoutée.');
        }

        $this->closeModal();
    }

    public function supprimer(Race $race): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($race->lapins()->exists()) {
            $this->flash("Impossible de supprimer « {$race->nom} » : des lapins y sont rattachés.", 'error');

            return;
        }

        $race->delete();
        $this->flash('Race supprimée.');
    }

    public function render()
    {
        return view('livewire.races.index', [
            'races' => Race::withCount('lapins')->orderBy('nom')->paginate(15),
        ]);
    }
}
