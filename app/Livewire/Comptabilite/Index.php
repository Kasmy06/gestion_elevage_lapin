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

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage;

    public int $mois;

    public int $annee;

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

    public function supprimerDepense(Depense $depense): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $depense->delete();

        $this->flash('Dépense supprimée.');
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
        $debutPeriode = Carbon::create($this->annee, $this->mois, 1)->startOfMonth();
        $finPeriode = $debutPeriode->copy()->endOfMonth();

        $recettes = $this->recettes($debutPeriode, $finPeriode);
        $depenses = $this->depenses($debutPeriode, $finPeriode);

        $moisChart = collect(range(5, 0))->map(fn ($i) => Carbon::today()->subMonths($i)->startOfMonth());

        return view('livewire.comptabilite.index', [
            'recettes' => $recettes,
            'depenses' => $depenses,
            'profit' => $recettes - $depenses,
            'dernieresVentes' => Vente::whereBetween('date', [$debutPeriode->toDateString(), $finPeriode->toDateString()])->with('client')->orderByDesc('date')->limit(5)->get(),
            'dernieresDepenses' => Depense::whereBetween('date', [$debutPeriode->toDateString(), $finPeriode->toDateString()])->orderByDesc('date')->limit(5)->get(),
            'chartLabels' => $moisChart->map(fn (Carbon $m) => ucfirst($m->translatedFormat('M Y')))->all(),
            'chartRecettes' => $moisChart->map(fn (Carbon $m) => $this->recettes($m->copy()->startOfMonth(), $m->copy()->endOfMonth()))->all(),
            'chartDepenses' => $moisChart->map(fn (Carbon $m) => $this->depenses($m->copy()->startOfMonth(), $m->copy()->endOfMonth()))->all(),
        ]);
    }
}
