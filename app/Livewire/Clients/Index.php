<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?Client $editing = null;

    public string $nom = '';

    public ?string $telephone = null;

    public ?string $email = null;

    public ?string $adresse = null;

    public ?string $notes = null;

    public function creer(): void
    {
        $this->reset(['editing', 'nom', 'telephone', 'email', 'adresse', 'notes']);
        $this->showModal = true;
    }

    public function modifier(Client $client): void
    {
        $this->editing = $client;
        $this->nom = $client->nom;
        $this->telephone = $client->telephone;
        $this->email = $client->email;
        $this->adresse = $client->adresse;
        $this->notes = $client->notes;
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
            'telephone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'adresse' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'notes' => $this->notes,
        ];

        if ($this->editing) {
            $this->editing->update($data);
            $this->flash('Client '.$this->nom.' mis à jour.');
        } else {
            Client::create($data);
            $this->flash('Client '.$this->nom.' ajouté.');
        }

        $this->closeModal();
    }

    public function supprimer(Client $client): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($client->ventes()->exists()) {
            $this->flash("Impossible de supprimer {$client->nom} : des ventes lui sont rattachées.", 'error');

            return;
        }

        $client->delete();
        $this->flash('Client supprimé.');
    }

    public function render()
    {
        return view('livewire.clients.index', [
            'clients' => Client::withCount('ventes')->orderBy('nom')->paginate(15),
        ]);
    }
}
