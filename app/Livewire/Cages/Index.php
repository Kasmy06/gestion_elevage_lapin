<?php

namespace App\Livewire\Cages;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Cage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?Cage $editing = null;

    public string $numero = '';

    public ?string $emplacement = null;

    public string $type = 'individuelle';

    public int $capacite = 1;

    public ?string $notes = null;

    public function creer(): void
    {
        $this->reset(['editing', 'numero', 'emplacement', 'notes']);
        $this->type = 'individuelle';
        $this->capacite = 1;
        $this->showModal = true;
    }

    public function modifier(Cage $cage): void
    {
        $this->editing = $cage;
        $this->numero = $cage->numero;
        $this->emplacement = $cage->emplacement;
        $this->type = $cage->type;
        $this->capacite = $cage->capacite;
        $this->notes = $cage->notes;
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
            'numero' => ['required', 'string', 'max:50', Rule::unique('cages', 'numero')->ignore($this->editing?->id)],
            'emplacement' => ['nullable', 'string', 'max:100'],
            'type' => ['required', Rule::in(array_keys(Cage::TYPES))],
            'capacite' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'numero' => $this->numero,
            'emplacement' => $this->emplacement,
            'type' => $this->type,
            'capacite' => $this->capacite,
            'notes' => $this->notes,
        ];

        if ($this->editing) {
            $this->editing->update($data);
            $this->flash('Clapier '.$this->numero.' mis à jour.');
        } else {
            Cage::create($data);
            $this->flash('Clapier '.$this->numero.' ajouté.');
        }

        $this->closeModal();
    }

    public function supprimer(Cage $cage): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($cage->lapins()->exists()) {
            $this->flash("Impossible de supprimer le clapier {$cage->numero} : des lapins y sont logés.", 'error');

            return;
        }

        $cage->delete();
        $this->flash('Clapier supprimé.');
    }

    public function render()
    {
        return view('livewire.cages.index', [
            'cages' => Cage::withCount('lapins')->orderBy('numero')->paginate(15),
        ]);
    }
}
