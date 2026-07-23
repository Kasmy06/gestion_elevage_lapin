<?php

namespace App\Livewire\Ventes;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Vente;
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

    public function restaurer(int $venteId): void
    {
        $vente = Vente::onlyTrashed()->findOrFail($venteId);
        $vente->restore();

        $this->flash('La vente « '.$vente->description.' » a été restaurée.');
    }

    public function supprimerDefinitivement(int $venteId): void
    {
        $vente = Vente::onlyTrashed()->findOrFail($venteId);
        $description = $vente->description;
        $vente->forceDelete();

        $this->flash('La vente « '.$description.' » a été supprimée définitivement.', 'error');
    }

    public function render()
    {
        $ventes = Vente::onlyTrashed()
            ->with('client')
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.ventes.corbeille', [
            'ventes' => $ventes,
        ]);
    }
}
