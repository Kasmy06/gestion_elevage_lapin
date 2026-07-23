<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Client;
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

    public function restaurer(int $clientId): void
    {
        $client = Client::onlyTrashed()->findOrFail($clientId);
        $client->restore();

        $this->flash('Le client '.$client->nom.' a été restauré.');
    }

    public function supprimerDefinitivement(int $clientId): void
    {
        $client = Client::onlyTrashed()->findOrFail($clientId);
        $nom = $client->nom;
        $client->forceDelete();

        $this->flash('Le client '.$nom.' a été supprimé définitivement.', 'error');
    }

    public function render()
    {
        $clients = Client::onlyTrashed()
            ->withCount('ventes')
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.clients.corbeille', [
            'clients' => $clients,
        ]);
    }
}
