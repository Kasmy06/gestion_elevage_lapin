<?php

namespace App\Livewire\Sorties;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Lapin;
use App\Models\Sortie;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?int $lapin_id = null;

    public string $type = 'vente';

    public string $date = '';

    public ?int $poids_g = null;

    public ?float $prix = null;

    public ?string $acheteur = null;

    public ?string $cause = null;

    public ?string $notes = null;

    public function creer(): void
    {
        $this->reset(['lapin_id', 'poids_g', 'prix', 'acheteur', 'cause', 'notes']);
        $this->type = 'vente';
        $this->date = Carbon::today()->toDateString();
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
            'lapin_id' => ['required', 'exists:lapins,id'],
            'type' => ['required', 'in:'.implode(',', array_keys(Sortie::TYPES))],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'poids_g' => ['nullable', 'integer', 'min:0'],
            'prix' => ['nullable', 'numeric', 'min:0'],
            'acheteur' => ['nullable', 'string', 'max:150'],
            'cause' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Sortie::create([
            'lapin_id' => $this->lapin_id,
            'type' => $this->type,
            'date' => $this->date,
            'poids_g' => $this->poids_g,
            'prix' => $this->prix,
            'acheteur' => $this->acheteur,
            'cause' => $this->cause,
            'notes' => $this->notes,
        ]);

        $this->flash('Sortie enregistrée.');
        $this->closeModal();
    }

    public function render()
    {
        $sorties = Sortie::query()
            ->with('lapin')
            ->orderByDesc('date')
            ->paginate(15);

        return view('livewire.sorties.index', [
            'sorties' => $sorties,
            'lapins' => Lapin::whereNotIn('statut', ['vendu', 'abattu', 'mort', 'donne'])->orderBy('identifiant')->get(),
        ]);
    }
}
