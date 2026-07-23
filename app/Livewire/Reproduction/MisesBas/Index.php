<?php

namespace App\Livewire\Reproduction\MisesBas;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Cage;
use App\Models\MiseBas;
use App\Models\Saillie;
use App\Models\Sevrage;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage;

    public string $modal = '';

    public ?MiseBas $selectedMiseBas = null;

    // Formulaire : sevrage
    public string $date_sevrage = '';

    public int $nb_sevres = 0;

    public ?int $poids_moyen_g = null;

    public ?int $cage_id = null;

    // Fiche du sevrage sélectionné pour la génération des lapereaux.
    // Stockés en propriétés scalaires plutôt que via une relation chargée sur
    // $selectedMiseBas, qui n'est pas préservée par Livewire entre deux appels.
    public ?int $selectedSevrageId = null;

    public int $selectedSevrageNbSevres = 0;

    public function ouvrirSevrage(int $miseBasId): void
    {
        $this->selectedMiseBas = MiseBas::findOrFail($miseBasId);
        $this->date_sevrage = Carbon::today()->toDateString();
        $this->nb_sevres = $this->selectedMiseBas->nb_nes_vivants;
        $this->poids_moyen_g = null;
        $this->cage_id = null;
        $this->modal = 'sevrage';
    }

    public function ouvrirGenerationLapereaux(int $miseBasId): void
    {
        $miseBas = MiseBas::with('sevrage')->findOrFail($miseBasId);

        $this->selectedMiseBas = $miseBas;
        $this->selectedSevrageId = $miseBas->sevrage->id;
        $this->selectedSevrageNbSevres = $miseBas->sevrage->nb_sevres;
        $this->cage_id = null;
        $this->modal = 'lapereaux';
    }

    public function closeModal(): void
    {
        $this->modal = '';
        $this->selectedMiseBas = null;
        $this->selectedSevrageId = null;
        $this->resetErrorBag();
    }

    public function enregistrerSevrage(): void
    {
        $this->validate([
            'date_sevrage' => ['required', 'date', 'before_or_equal:today'],
            'nb_sevres' => ['required', 'integer', 'min:0', 'max:'.$this->selectedMiseBas->nb_nes_vivants],
            'poids_moyen_g' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->selectedMiseBas->sevrage()->create([
            'date_sevrage' => $this->date_sevrage,
            'nb_sevres' => $this->nb_sevres,
            'poids_moyen_g' => $this->poids_moyen_g,
        ]);

        $this->flash('Sevrage enregistré pour '.$this->nb_sevres.' lapereau(x).');
        $this->closeModal();
    }

    public function genererLapereaux(): void
    {
        $sevrage = Sevrage::findOrFail($this->selectedSevrageId);
        $lapereaux = $sevrage->genererLapereaux($this->cage_id);

        $this->flash($lapereaux->count().' fiche(s) lapereau créée(s). Pensez à les sexer dès que possible (chapitre 3.2 de l\'Agrodok).');
        $this->closeModal();
    }

    public function render()
    {
        $misesBas = MiseBas::query()
            ->with(['femelle', 'sevrage'])
            ->orderByDesc('date_mise_bas')
            ->paginate(15);

        $sailliesEnAttenteMiseBas = Saillie::where('diagnostic_gestation', 'positif')
            ->whereDoesntHave('miseBas')
            ->with('femelle')
            ->orderBy('date_mise_bas_prevue')
            ->get();

        return view('livewire.reproduction.mises-bas.index', [
            'misesBas' => $misesBas,
            'cages' => Cage::orderBy('numero')->get(),
            'sailliesEnAttenteMiseBas' => $sailliesEnAttenteMiseBas,
        ]);
    }
}
