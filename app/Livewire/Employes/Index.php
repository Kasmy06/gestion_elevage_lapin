<?php

namespace App\Livewire\Employes;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Employe;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?Employe $editing = null;

    public string $nom = '';

    public string $poste = '';

    public ?string $telephone = null;

    public ?string $email = null;

    public ?string $date_embauche = null;

    public ?float $salaire = null;

    public string $statut = 'actif';

    public ?string $notes = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function creer(): void
    {
        $this->reset(['editing', 'nom', 'poste', 'telephone', 'email', 'date_embauche', 'salaire', 'notes']);
        $this->statut = 'actif';
        $this->showModal = true;
    }

    public function modifier(Employe $employe): void
    {
        $this->editing = $employe;
        $this->nom = $employe->nom;
        $this->poste = $employe->poste;
        $this->telephone = $employe->telephone;
        $this->email = $employe->email;
        $this->date_embauche = $employe->date_embauche?->toDateString();
        $this->salaire = $employe->salaire;
        $this->statut = $employe->statut;
        $this->notes = $employe->notes;
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
            'nom' => ['required', 'string', 'max:150'],
            'poste' => ['required', 'string', 'max:100'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'date_embauche' => ['nullable', 'date'],
            'salaire' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['required', Rule::in(array_keys(Employe::STATUTS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'nom' => $this->nom,
            'poste' => $this->poste,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'date_embauche' => $this->date_embauche,
            'salaire' => $this->salaire,
            'statut' => $this->statut,
            'notes' => $this->notes,
        ];

        if ($this->editing) {
            $this->editing->update($data);
            $this->flash('Employé '.$this->nom.' mis à jour.');
        } else {
            Employe::create($data);
            $this->flash('Employé '.$this->nom.' ajouté.');
        }

        $this->closeModal();
    }

    public function supprimer(Employe $employe): void
    {
        $employe->delete();
        $this->flash('Employé supprimé.');
    }

    public function render()
    {
        return view('livewire.employes.index', [
            'employes' => Employe::orderBy('nom')->paginate(15),
        ]);
    }
}
