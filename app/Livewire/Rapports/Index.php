<?php

namespace App\Livewire\Rapports;

use App\Models\Depense;
use App\Models\Lapin;
use App\Models\MouvementAliment;
use App\Models\Pesee;
use App\Models\Saillie;
use App\Models\SanteIntervention;
use App\Models\Vente;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class Index extends Component
{
    private function telecharger(string $nomFichier, array $entetes, iterable $lignes): StreamedResponse
    {
        return response()->streamDownload(function () use ($entetes, $lignes) {
            $sortie = fopen('php://output', 'w');
            fputcsv($sortie, $entetes);

            foreach ($lignes as $ligne) {
                fputcsv($sortie, $ligne);
            }

            fclose($sortie);
        }, $nomFichier, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exporterCheptel(): StreamedResponse
    {
        $lignes = Lapin::with('race', 'cage')->orderBy('identifiant')->get()->map(fn (Lapin $l) => [
            $l->identifiant, $l->race?->nom, $l->sexe ?? 'non sexé', Lapin::STATUTS[$l->statut],
            $l->date_naissance?->format('d/m/Y'), $l->poids_actuel_g, $l->cage?->numero,
        ]);

        return $this->telecharger('rapport-cheptel.csv', ['Identifiant', 'Race', 'Sexe', 'Statut', 'Naissance', 'Poids (g)', 'Clapier'], $lignes);
    }

    public function exporterReproduction(): StreamedResponse
    {
        $lignes = Saillie::with('femelle', 'male', 'miseBas.sevrage')->orderByDesc('date_saillie')->get()->map(fn (Saillie $s) => [
            $s->femelle->identifiant, $s->male->identifiant, $s->date_saillie->format('d/m/Y'),
            Saillie::DIAGNOSTICS[$s->diagnostic_gestation], $s->date_mise_bas_prevue->format('d/m/Y'),
            $s->miseBas?->date_mise_bas?->format('d/m/Y'), $s->miseBas?->nb_nes_vivants, $s->miseBas?->sevrage?->date_sevrage?->format('d/m/Y'),
        ]);

        return $this->telecharger('rapport-reproduction.csv', ['Femelle', 'Mâle', 'Date saillie', 'Diagnostic', 'Mise bas prévue', 'Mise bas', 'Nés vivants', 'Sevrage'], $lignes);
    }

    public function exporterSante(): StreamedResponse
    {
        $lignes = SanteIntervention::with('lapin')->orderByDesc('date_debut')->get()->map(fn (SanteIntervention $s) => [
            $s->lapin->identifiant, SanteIntervention::TYPES[$s->type], $s->libelle,
            $s->date_debut->format('d/m/Y'), SanteIntervention::STATUTS[$s->statut], $s->cout,
        ]);

        return $this->telecharger('rapport-sante.csv', ['Lapin', 'Type', 'Libellé', 'Date', 'Statut', 'Coût'], $lignes);
    }

    public function exporterCroissance(): StreamedResponse
    {
        $lignes = Pesee::with('lapin')->orderByDesc('date_pesee')->get()->map(fn (Pesee $p) => [
            $p->lapin->identifiant, $p->date_pesee->format('d/m/Y'), $p->poids_g,
        ]);

        return $this->telecharger('rapport-croissance.csv', ['Lapin', 'Date', 'Poids (g)'], $lignes);
    }

    public function exporterFinancier(): StreamedResponse
    {
        $ventes = Vente::orderByDesc('date')->get()->map(fn (Vente $v) => ['Recette', $v->date->format('d/m/Y'), $v->description, $v->montant_total]);
        $depenses = Depense::orderByDesc('date')->get()->map(fn (Depense $d) => ['Dépense', $d->date->format('d/m/Y'), $d->libelle, $d->montant]);

        return $this->telecharger('rapport-financier.csv', ['Type', 'Date', 'Description', 'Montant'], $ventes->concat($depenses));
    }

    public function exporterDistributionParClapier(): StreamedResponse
    {
        $lignes = MouvementAliment::query()
            ->select('cage_id', 'aliment_id')
            ->selectRaw('SUM(quantite) as quantite_totale, SUM(cout) as cout_total')
            ->where('type_mouvement', 'distribution')
            ->groupBy('cage_id', 'aliment_id')
            ->with(['cage', 'aliment'])
            ->get()
            ->sort(fn ($a, $b) => strcmp($a->cage?->numero ?? '', $b->cage?->numero ?? '') ?: strcmp($a->aliment->nom, $b->aliment->nom))
            ->values()
            ->map(fn (MouvementAliment $m) => [
                $m->cage?->numero ?? 'Non précisé',
                $m->aliment->nom,
                rtrim(rtrim((string) $m->quantite_totale, '0'), '.'),
                $m->aliment->unite,
                $m->cout_total,
            ]);

        return $this->telecharger('rapport-distribution-clapiers.csv', ['Clapier', 'Aliment', 'Quantité totale distribuée', 'Unité', 'Coût total'], $lignes);
    }

    public function render()
    {
        $debutMois = Carbon::today()->startOfMonth();

        return view('livewire.rapports.index', [
            'totalLapins' => Lapin::count(),
            'totalSaillies' => Saillie::count(),
            'suiviSanteEnCours' => SanteIntervention::where('statut', 'en_cours')->count(),
            'peseesCeMois' => Pesee::where('date_pesee', '>=', $debutMois)->count(),
            'ventesCeMois' => (float) Vente::where('date', '>=', $debutMois)->sum('montant_total'),
            'depensesCeMois' => (float) Depense::where('date', '>=', $debutMois)->sum('montant'),
        ]);
    }
}
