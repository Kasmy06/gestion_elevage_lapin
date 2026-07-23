<?php

namespace App\Livewire\Alimentation\Mouvements;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Aliment;
use App\Models\Cage;
use App\Models\MouvementAliment;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    #[Url(history: true)]
    public ?int $cageFiltre = null;

    public bool $showModal = false;

    public ?int $aliment_id = null;

    public ?int $cage_id = null;

    public string $date = '';

    public string $type_mouvement = 'distribution';

    public ?float $quantite = null;

    public ?float $cout = null;

    public ?string $notes = null;

    public function creer(): void
    {
        $this->reset(['aliment_id', 'cage_id', 'quantite', 'cout', 'notes']);
        $this->date = Carbon::today()->toDateString();
        $this->type_mouvement = 'distribution';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function updating($property): void
    {
        if ($property === 'cageFiltre') {
            $this->resetPage();
        }
    }

    public function save(): void
    {
        $this->validate([
            'aliment_id' => ['required', 'exists:aliments,id'],
            'cage_id' => ['nullable', 'exists:cages,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'type_mouvement' => ['required', 'in:entree,distribution'],
            'quantite' => ['required', 'numeric', 'min:0.01'],
            'cout' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($this->type_mouvement === 'distribution') {
            $aliment = Aliment::find($this->aliment_id);
            if ($aliment && $this->quantite > $aliment->stock_actuel) {
                $this->addError('quantite', "Stock insuffisant ({$aliment->stock_actuel} {$aliment->unite} disponible(s)).");

                return;
            }
        }

        MouvementAliment::create([
            'aliment_id' => $this->aliment_id,
            'cage_id' => $this->cage_id,
            'date' => $this->date,
            'type_mouvement' => $this->type_mouvement,
            'quantite' => $this->quantite,
            'cout' => $this->cout,
            'notes' => $this->notes,
        ]);

        $this->flash('Mouvement de stock enregistré.');
        $this->closeModal();
    }

    public function render()
    {
        $mouvements = MouvementAliment::query()
            ->with(['aliment', 'cage'])
            ->when($this->cageFiltre, fn ($query) => $query->where('cage_id', $this->cageFiltre))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.alimentation.mouvements.index', [
            'mouvements' => $mouvements,
            'aliments' => Aliment::orderBy('nom')->get(),
            'cages' => Cage::orderBy('numero')->get(),
        ]);
    }
}
