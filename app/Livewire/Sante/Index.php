<?php

namespace App\Livewire\Sante;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Lapin;
use App\Models\SanteIntervention;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    #[Url]
    public string $statutFiltre = '';

    public bool $showModal = false;

    public ?int $lapin_id = null;

    public string $type = 'maladie';

    public string $libelle = '';

    public string $date_debut = '';

    public ?string $traitement_applique = null;

    public ?float $cout = null;

    public ?string $notes = null;

    public function updatingStatutFiltre(): void
    {
        $this->resetPage();
    }

    public function creer(): void
    {
        $this->reset(['lapin_id', 'libelle', 'traitement_applique', 'cout', 'notes']);
        $this->type = 'maladie';
        $this->date_debut = Carbon::today()->toDateString();
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
            'type' => ['required', 'in:'.implode(',', array_keys(SanteIntervention::TYPES))],
            'libelle' => ['required', 'string', 'max:150'],
            'date_debut' => ['required', 'date', 'before_or_equal:today'],
            'traitement_applique' => ['nullable', 'string', 'max:1000'],
            'cout' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        SanteIntervention::create([
            'lapin_id' => $this->lapin_id,
            'type' => $this->type,
            'libelle' => $this->libelle,
            'date_debut' => $this->date_debut,
            'statut' => 'en_cours',
            'traitement_applique' => $this->traitement_applique,
            'cout' => $this->cout,
            'notes' => $this->notes,
        ]);

        $this->flash('Suivi santé enregistré.');
        $this->closeModal();
    }

    public function cloturer(SanteIntervention $intervention, string $statut): void
    {
        $intervention->update([
            'statut' => $statut,
            'date_fin' => Carbon::today(),
        ]);

        $this->flash($statut === 'gueri' ? 'Le lapin est marqué comme guéri.' : 'Décès enregistré.', $statut === 'deces' ? 'error' : 'success');
    }

    public function render()
    {
        $interventions = SanteIntervention::query()
            ->with('lapin')
            ->when($this->statutFiltre, fn ($q) => $q->where('statut', $this->statutFiltre))
            ->orderByDesc('date_debut')
            ->paginate(15);

        return view('livewire.sante.index', [
            'interventions' => $interventions,
            'lapins' => Lapin::orderBy('identifiant')->get(),
        ]);
    }
}
