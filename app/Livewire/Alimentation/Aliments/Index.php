<?php

namespace App\Livewire\Alimentation\Aliments;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Aliment;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?Aliment $editing = null;

    public string $nom = '';

    public string $type = 'concentre';

    public string $unite = 'kg';

    public float $stock_actuel = 0;

    public ?float $seuil_alerte = null;

    public function creer(): void
    {
        $this->reset(['editing', 'nom', 'seuil_alerte']);
        $this->type = 'concentre';
        $this->unite = 'kg';
        $this->stock_actuel = 0;
        $this->showModal = true;
    }

    public function modifier(Aliment $aliment): void
    {
        $this->editing = $aliment;
        $this->nom = $aliment->nom;
        $this->type = $aliment->type;
        $this->unite = $aliment->unite;
        $this->stock_actuel = $aliment->stock_actuel;
        $this->seuil_alerte = $aliment->seuil_alerte;
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
            'nom' => ['required', 'string', 'max:100', Rule::unique('aliments', 'nom')->ignore($this->editing?->id)],
            'type' => ['required', Rule::in(array_keys(Aliment::TYPES))],
            'unite' => ['required', 'string', 'max:20'],
            'stock_actuel' => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data = [
            'nom' => $this->nom,
            'type' => $this->type,
            'unite' => $this->unite,
            'stock_actuel' => $this->stock_actuel,
            'seuil_alerte' => $this->seuil_alerte,
        ];

        if ($this->editing) {
            $this->editing->update($data);
            $this->flash('Aliment « '.$this->nom.' » mis à jour.');
        } else {
            Aliment::create($data);
            $this->flash('Aliment « '.$this->nom.' » ajouté.');
        }

        $this->closeModal();
    }

    public function supprimer(Aliment $aliment): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $aliment->delete();
        $this->flash('Aliment supprimé.');
    }

    public function render()
    {
        return view('livewire.alimentation.aliments.index', [
            'aliments' => Aliment::orderBy('nom')->paginate(15),
        ]);
    }
}
