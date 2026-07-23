<?php

namespace App\Livewire\Reproduction\Saillies;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Lapin;
use App\Models\MiseBas;
use App\Models\Saillie;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage;

    public string $modal = '';

    public ?Saillie $selectedSaillie = null;

    // Formulaire : nouvelle saillie
    public ?int $male_id = null;

    public ?int $femelle_id = null;

    public string $date_saillie = '';

    // Formulaire : diagnostic de gestation
    public string $diagnostic_gestation = 'positif';

    public string $date_diagnostic = '';

    // Formulaire : mise bas
    public string $date_mise_bas = '';

    public int $nb_nes_vivants = 0;

    public int $nb_morts_nes = 0;

    public function ouvrirCreation(): void
    {
        $this->reset(['male_id', 'femelle_id']);
        $this->date_saillie = Carbon::today()->toDateString();
        $this->modal = 'create';
    }

    public function ouvrirDiagnostic(int $saillieId): void
    {
        $this->selectedSaillie = Saillie::findOrFail($saillieId);
        $this->diagnostic_gestation = 'positif';
        $this->date_diagnostic = Carbon::today()->toDateString();
        $this->modal = 'diagnostic';
    }

    public function ouvrirMiseBas(int $saillieId): void
    {
        $this->selectedSaillie = Saillie::findOrFail($saillieId);
        $this->date_mise_bas = $this->selectedSaillie->date_mise_bas_prevue->toDateString();
        $this->nb_nes_vivants = 0;
        $this->nb_morts_nes = 0;
        $this->modal = 'mise-bas';
    }

    public function closeModal(): void
    {
        $this->modal = '';
        $this->selectedSaillie = null;
        $this->resetErrorBag();
    }

    public function enregistrerSaillie(): void
    {
        $this->validate([
            'male_id' => ['required', 'different:femelle_id', 'exists:lapins,id'],
            'femelle_id' => ['required', 'exists:lapins,id'],
            'date_saillie' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'male_id.different' => 'Le mâle et la femelle doivent être différents.',
        ]);

        Saillie::create([
            'male_id' => $this->male_id,
            'femelle_id' => $this->femelle_id,
            'date_saillie' => $this->date_saillie,
            'diagnostic_gestation' => 'en_attente',
        ]);

        $this->flash('Saillie enregistrée. Diagnostic de gestation possible à partir du '.Carbon::parse($this->date_saillie)->addDays(Saillie::JOURS_AVANT_DIAGNOSTIC)->format('d/m/Y').'.');
        $this->closeModal();
    }

    public function enregistrerDiagnostic(): void
    {
        $this->validate([
            'diagnostic_gestation' => ['required', 'in:positif,negatif'],
            'date_diagnostic' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $this->selectedSaillie->update([
            'diagnostic_gestation' => $this->diagnostic_gestation,
            'date_diagnostic' => $this->date_diagnostic,
        ]);

        $this->flash('Diagnostic enregistré : '.($this->diagnostic_gestation === 'positif' ? 'la lapine est gestante.' : 'saillie négative.'));
        $this->closeModal();
    }

    public function enregistrerMiseBas(): void
    {
        $this->validate([
            'date_mise_bas' => ['required', 'date', 'before_or_equal:today'],
            'nb_nes_vivants' => ['required', 'integer', 'min:0'],
            'nb_morts_nes' => ['required', 'integer', 'min:0'],
        ]);

        MiseBas::create([
            'saillie_id' => $this->selectedSaillie->id,
            'femelle_id' => $this->selectedSaillie->femelle_id,
            'date_mise_bas' => $this->date_mise_bas,
            'nb_nes_vivants' => $this->nb_nes_vivants,
            'nb_morts_nes' => $this->nb_morts_nes,
        ]);

        $this->flash('Mise bas enregistrée : '.$this->nb_nes_vivants.' lapereau(x) vivant(s). Rendez-vous dans « Mises bas & sevrages » pour suivre le sevrage.');
        $this->closeModal();
    }

    public function render()
    {
        $saillies = Saillie::query()
            ->with(['male', 'femelle', 'miseBas'])
            ->orderByDesc('date_saillie')
            ->paginate(15);

        return view('livewire.reproduction.saillies.index', [
            'saillies' => $saillies,
            'males' => Lapin::disponiblesPourSaillie('male')->orderBy('identifiant')->get(),
            'femelles' => Lapin::disponiblesPourSaillie('femelle')->orderBy('identifiant')->get(),
        ]);
    }
}
