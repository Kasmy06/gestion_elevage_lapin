<?php

namespace App\Livewire\Alimentation\Aliments;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Aliment;
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

    public function restaurer(int $alimentId): void
    {
        $aliment = Aliment::onlyTrashed()->findOrFail($alimentId);
        $aliment->restore();

        $this->flash('L\'aliment « '.$aliment->nom.' » a été restauré.');
    }

    public function supprimerDefinitivement(int $alimentId): void
    {
        $aliment = Aliment::onlyTrashed()->findOrFail($alimentId);
        $nom = $aliment->nom;
        $aliment->forceDelete();

        $this->flash('L\'aliment « '.$nom.' » a été supprimé définitivement.', 'error');
    }

    public function render()
    {
        $aliments = Aliment::onlyTrashed()
            ->withCount('mouvements')
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('livewire.alimentation.aliments.corbeille', [
            'aliments' => $aliments,
        ]);
    }
}
