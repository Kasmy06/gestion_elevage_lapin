<?php

namespace App\Livewire\Reproduction\MisesBas;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Cage;
use App\Models\MiseBas;
use App\Models\MortaliteLapereaux;
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

    // Formulaire : modification de la mise bas
    public string $date_mise_bas = '';

    public int $nb_nes_vivants = 0;

    public int $nb_morts_nes = 0;

    public ?string $notes = null;

    // Formulaire : décès de lapereaux
    public string $mortaliteStade = MortaliteLapereaux::STADE_NAISSANCE;

    public string $mortaliteDate = '';

    public int $mortaliteNombre = 1;

    public ?string $mortaliteNotes = null;

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

    public function ouvrirModification(int $miseBasId): void
    {
        $this->selectedMiseBas = MiseBas::with('sevrage')->findOrFail($miseBasId);
        $this->date_mise_bas = $this->selectedMiseBas->date_mise_bas->toDateString();
        $this->nb_nes_vivants = $this->selectedMiseBas->nb_nes_vivants;
        $this->nb_morts_nes = $this->selectedMiseBas->nb_morts_nes;
        $this->notes = $this->selectedMiseBas->notes;
        $this->modal = 'edit';
    }

    public function modifierMiseBas(): void
    {
        $this->validate([
            'date_mise_bas' => ['required', 'date', 'before_or_equal:today'],
            'nb_nes_vivants' => ['required', 'integer', 'min:0'],
            'nb_morts_nes' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($this->selectedMiseBas->sevrage && $this->nb_nes_vivants < $this->selectedMiseBas->sevrage->nb_sevres) {
            $this->addError('nb_nes_vivants', 'Le nombre de nés vivants ne peut pas être inférieur au nombre déjà sevré ('.$this->selectedMiseBas->sevrage->nb_sevres.').');

            return;
        }

        $this->selectedMiseBas->update([
            'date_mise_bas' => $this->date_mise_bas,
            'nb_nes_vivants' => $this->nb_nes_vivants,
            'nb_morts_nes' => $this->nb_morts_nes,
            'notes' => $this->notes,
        ]);

        $this->flash('Mise bas mise à jour.');
        $this->closeModal();
    }

    public function ouvrirMortalite(int $miseBasId): void
    {
        $this->selectedMiseBas = MiseBas::with('sevrage')->findOrFail($miseBasId);
        $this->mortaliteStade = MortaliteLapereaux::STADE_NAISSANCE;
        $this->mortaliteDate = Carbon::today()->toDateString();
        $this->mortaliteNombre = 1;
        $this->mortaliteNotes = null;
        $this->modal = 'mortalite';
    }

    public function enregistrerMortalite(): void
    {
        $this->validate([
            'mortaliteStade' => ['required', 'in:'.implode(',', array_keys(MortaliteLapereaux::STADES))],
            'mortaliteDate' => ['required', 'date', 'before_or_equal:today'],
            'mortaliteNombre' => ['required', 'integer', 'min:1'],
            'mortaliteNotes' => ['nullable', 'string', 'max:1000'],
        ]);

        $miseBas = $this->selectedMiseBas;
        $sevrage = $miseBas->sevrage;

        if ($this->mortaliteStade === MortaliteLapereaux::STADE_NAISSANCE) {
            if ($sevrage) {
                $this->addError('mortaliteStade', 'Le sevrage est déjà enregistré : déclarez ce décès comme « après sevrage ».');

                return;
            }

            $disponibles = $miseBas->nb_nes_vivants - $miseBas->nbMortsAuStade(MortaliteLapereaux::STADE_NAISSANCE);
        } else {
            if (! $sevrage) {
                $this->addError('mortaliteStade', 'Enregistrez d\'abord le sevrage pour déclarer un décès après sevrage.');

                return;
            }

            if ($sevrage->lapereaux_generes) {
                $this->addError('mortaliteStade', 'Les fiches des lapereaux sont déjà créées : déclarez ce décès sur la fiche du lapin concerné.');

                return;
            }

            $disponibles = $sevrage->nbLapereauxAIdentifier();
        }

        if ($this->mortaliteNombre > $disponibles) {
            $this->addError('mortaliteNombre', 'Il ne reste que '.$disponibles.' lapereau(x) vivant(s) à ce stade.');

            return;
        }

        $miseBas->mortalites()->create([
            'date' => $this->mortaliteDate,
            'stade' => $this->mortaliteStade,
            'nombre' => $this->mortaliteNombre,
            'notes' => $this->mortaliteNotes,
        ]);

        $this->flash('Décès de '.$this->mortaliteNombre.' lapereau(x) enregistré ('.MortaliteLapereaux::STADES[$this->mortaliteStade].').');
        $this->closeModal();
    }

    public function ouvrirSevrage(int $miseBasId): void
    {
        $this->selectedMiseBas = MiseBas::findOrFail($miseBasId);
        $this->date_sevrage = Carbon::today()->toDateString();
        $this->nb_sevres = $this->selectedMiseBas->nb_nes_vivants - $this->selectedMiseBas->nbMortsAuStade(MortaliteLapereaux::STADE_NAISSANCE);
        $this->poids_moyen_g = null;
        $this->cage_id = null;
        $this->modal = 'sevrage';
    }

    public function ouvrirGenerationLapereaux(int $miseBasId): void
    {
        $miseBas = MiseBas::with('sevrage')->findOrFail($miseBasId);

        $this->selectedMiseBas = $miseBas;
        $this->selectedSevrageId = $miseBas->sevrage->id;
        $this->selectedSevrageNbSevres = $miseBas->sevrage->nbLapereauxAIdentifier();
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
            'nb_sevres' => ['required', 'integer', 'min:0', 'max:'.($this->selectedMiseBas->nb_nes_vivants - $this->selectedMiseBas->nbMortsAuStade(MortaliteLapereaux::STADE_NAISSANCE))],
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
            ->with(['femelle', 'sevrage', 'mortalites'])
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
