<?php

namespace App\Livewire\Ventes;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Client;
use App\Models\Vente;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?int $client_id = null;

    public string $description = '';

    public float $quantite = 1;

    public ?float $prix_unitaire = null;

    public string $date = '';

    public string $mode_paiement = 'especes';

    public string $statut_paiement = 'paye';

    public ?string $notes = null;

    public function creer(): void
    {
        $this->reset(['client_id', 'description', 'prix_unitaire', 'notes']);
        $this->quantite = 1;
        $this->date = Carbon::today()->toDateString();
        $this->mode_paiement = 'especes';
        $this->statut_paiement = 'paye';
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
            'client_id' => ['nullable', 'exists:clients,id'],
            'description' => ['required', 'string', 'max:150'],
            'quantite' => ['required', 'numeric', 'min:0.01'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'mode_paiement' => ['required', 'in:'.implode(',', array_keys(Vente::MODES_PAIEMENT))],
            'statut_paiement' => ['required', 'in:'.implode(',', array_keys(Vente::STATUTS_PAIEMENT))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Vente::create([
            'client_id' => $this->client_id,
            'description' => $this->description,
            'quantite' => $this->quantite,
            'prix_unitaire' => $this->prix_unitaire,
            'date' => $this->date,
            'mode_paiement' => $this->mode_paiement,
            'statut_paiement' => $this->statut_paiement,
            'notes' => $this->notes,
        ]);

        $this->flash('Vente enregistrée.');
        $this->closeModal();
    }

    public function supprimer(Vente $vente): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $vente->delete();

        $this->flash('Vente supprimée.');
    }

    public function render()
    {
        return view('livewire.ventes.index', [
            'ventes' => Vente::with('client')->orderByDesc('date')->orderByDesc('id')->paginate(15),
            'clients' => Client::orderBy('nom')->get(),
            'totalPeriode' => Vente::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('montant_total'),
        ]);
    }
}
