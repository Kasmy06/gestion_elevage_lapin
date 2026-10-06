<?php

namespace App\Livewire\Comptabilite;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Depense;
use App\Models\MouvementAliment;
use App\Models\SanteIntervention;
use App\Models\Vente;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public int $mois;

    public int $annee;

    public bool $toutesPeriodes = false;

    public bool $showModal = false;

    public string $categorie = 'autre';

    public string $libelle = '';

    public ?float $montant = null;

    public string $date = '';

    public ?string $notes = null;

    public function mount(): void
    {
        $this->mois = (int) now()->month;
        $this->annee = (int) now()->year;
    }

    public function updating($property): void
    {
        if (in_array($property, ['mois', 'annee', 'toutesPeriodes'], true)) {
            $this->resetPage();
        }
    }

    public function ouvrirDepense(): void
    {
        $this->reset(['libelle', 'montant', 'notes']);
        $this->categorie = 'autre';
        $this->date = Carbon::today()->toDateString();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function enregistrerDepense(): void
    {
        $this->validate([
            'categorie' => ['required', 'in:'.implode(',', array_keys(Depense::CATEGORIES))],
            'libelle' => ['required', 'string', 'max:150'],
            'montant' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Depense::create([
            'categorie' => $this->categorie,
            'libelle' => $this->libelle,
            'montant' => $this->montant,
            'date' => $this->date,
            'notes' => $this->notes,
        ]);

        $this->flash('Dépense enregistrée.');
        $this->closeModal();
    }

    private function periodeSelectionnee(): array
    {
        $debut = Carbon::create($this->annee, $this->mois, 1)->startOfMonth();

        return [$debut, $debut->copy()->endOfMonth()];
    }

    public function exporterDepenses(): StreamedResponse
    {
        if ($this->toutesPeriodes) {
            $depenses = Depense::orderByDesc('date')->orderByDesc('id')->get();
            $nomFichier = 'depenses-toutes.csv';
        } else {
            [$debutPeriode, $finPeriode] = $this->periodeSelectionnee();

            $depenses = Depense::whereBetween('date', [$debutPeriode->toDateString(), $finPeriode->toDateString()])
                ->orderByDesc('date')
                ->get();
            $nomFichier = 'depenses-'.$debutPeriode->format('Y-m').'.csv';
        }

        return response()->streamDownload(function () use ($depenses) {
            $sortie = fopen('php://output', 'w');
            fputcsv($sortie, ['Date', 'Libellé', 'Catégorie', 'Montant', 'Notes']);

            foreach ($depenses as $depense) {
                fputcsv($sortie, [
                    $depense->date->format('d/m/Y'),
                    $depense->libelle,
                    Depense::CATEGORIES[$depense->categorie],
                    $depense->montant,
                    $depense->notes,
                ]);
            }

            fclose($sortie);
        }, $nomFichier, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function recettes(Carbon $debut, Carbon $fin): float
    {
        return (float) Vente::whereBetween('date', [$debut->toDateString(), $fin->toDateString()])->sum('montant_total');
    }

    private function depenses(Carbon $debut, Carbon $fin): float
    {
        $bornes = [$debut->toDateString(), $fin->toDateString()];

        return (float) Depense::whereBetween('date', $bornes)->sum('montant')
            + (float) SanteIntervention::whereBetween('date_debut', $bornes)->sum('cout')
            + (float) MouvementAliment::where('type_mouvement', 'entree')->whereBetween('date', $bornes)->sum('cout');
    }

    public function render()
    {
        [$debutPeriode, $finPeriode] = $this->periodeSelectionnee();

        $recettes = $this->recettes($debutPeriode, $finPeriode);
        $depenses = $this->depenses($debutPeriode, $finPeriode);

        $moisChart = collect(range(5, 0))->map(fn ($i) => Carbon::today()->subMonths($i)->startOfMonth());

        return view('livewire.comptabilite.index', [
            'recettes' => $recettes,
            'depenses' => $depenses,
            'profit' => $recettes - $depenses,
            'dernieresVentes' => Vente::whereBetween('date', [$debutPeriode->toDateString(), $finPeriode->toDateString()])->with('client')->orderByDesc('date')->limit(5)->get(),
            'depensesPeriode' => $this->toutesPeriodes
                ? Depense::orderByDesc('date')->orderByDesc('id')->paginate(15)
                : Depense::whereBetween('date', [$debutPeriode->toDateString(), $finPeriode->toDateString()])->orderByDesc('date')->orderByDesc('id')->paginate(15),
            'chartLabels' => $moisChart->map(fn (Carbon $m) => ucfirst($m->translatedFormat('M Y')))->all(),
            'chartRecettes' => $moisChart->map(fn (Carbon $m) => $this->recettes($m->copy()->startOfMonth(), $m->copy()->endOfMonth()))->all(),
            'chartDepenses' => $moisChart->map(fn (Carbon $m) => $this->depenses($m->copy()->startOfMonth(), $m->copy()->endOfMonth()))->all(),
        ]);
    }
}
