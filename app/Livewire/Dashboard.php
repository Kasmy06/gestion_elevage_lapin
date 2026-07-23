<?php

namespace App\Livewire;

use App\Models\Lapin;
use App\Models\MiseBas;
use App\Models\Saillie;
use App\Support\AlertesElevage;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $diagnosticsAFaire = AlertesElevage::diagnosticsAFaire();
        $misesBasImminentes = AlertesElevage::misesBasImminentes();
        $sevragesImminents = AlertesElevage::sevragesImminents();
        $stocksBas = AlertesElevage::stocksBas();
        $suivisSante = AlertesElevage::suivisSanteEnCours();

        $femellesReproductrices = Lapin::where('sexe', 'femelle')
            ->where('statut', 'reproducteur')
            ->with('saillesFemelle.miseBas.sevrage')
            ->orderBy('identifiant')
            ->get();

        $femellesDisponibles = $femellesReproductrices->filter(function (Lapin $femelle) {
            $derniere = $femelle->saillesFemelle->sortByDesc('date_saillie')->first();

            if (! $derniere || $derniere->diagnostic_gestation === 'negatif') {
                return true;
            }

            if ($derniere->diagnostic_gestation === 'en_attente') {
                return false;
            }

            return $derniere->miseBas && $derniere->miseBas->sevrage;
        })->values();

        $naissancesParMois = MiseBas::where('date_mise_bas', '>=', Carbon::today()->subMonths(5)->startOfMonth())
            ->get()
            ->groupBy(fn (MiseBas $m) => $m->date_mise_bas->format('Y-m'))
            ->map(fn ($groupe) => $groupe->sum('nb_nes_vivants'));

        $moisChart = collect(range(5, 0))->map(fn ($i) => Carbon::today()->subMonths($i));

        return view('livewire.dashboard', [
            'totalActifs' => Lapin::whereNotIn('statut', ['vendu', 'abattu', 'mort', 'donne'])->count(),
            'totalReproducteurs' => Lapin::where('statut', 'reproducteur')->count(),
            'gestantes' => Saillie::where('diagnostic_gestation', 'positif')->whereDoesntHave('miseBas')->count(),
            'naissancesDuMois' => (int) MiseBas::whereMonth('date_mise_bas', now()->month)->whereYear('date_mise_bas', now()->year)->sum('nb_nes_vivants'),
            'alertesCount' => $diagnosticsAFaire->count() + $misesBasImminentes->count() + $sevragesImminents->count() + $stocksBas->count(),
            'diagnosticsAFaire' => $diagnosticsAFaire,
            'misesBasImminentes' => $misesBasImminentes,
            'sevragesImminents' => $sevragesImminents,
            'femellesDisponibles' => $femellesDisponibles,
            'stocksBas' => $stocksBas,
            'suivisSante' => $suivisSante,
            'chartLabels' => $moisChart->map(fn (Carbon $m) => ucfirst($m->translatedFormat('M Y')))->all(),
            'chartNaissances' => $moisChart->map(fn (Carbon $m) => (int) ($naissancesParMois[$m->format('Y-m')] ?? 0))->all(),
            'repartitionLabels' => array_values(Lapin::STATUTS),
            'repartitionData' => array_map(
                fn ($statut) => Lapin::where('statut', $statut)->count(),
                array_keys(Lapin::STATUTS)
            ),
        ]);
    }
}
