<?php

namespace App\Livewire\Lapins;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Lapin;
use App\Models\Pesee;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    use HasFlashMessage;

    public Lapin $lapin;

    public string $nouvellePeseeDate = '';

    public ?int $nouveauPoidsG = null;

    public function mount(Lapin $lapin): void
    {
        $this->lapin = $lapin;
        $this->nouvellePeseeDate = Carbon::today()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'nouvellePeseeDate' => ['required', 'date', 'before_or_equal:today'],
            'nouveauPoidsG' => ['required', 'integer', 'min:1'],
        ];
    }

    public function ajouterPesee(): void
    {
        $this->validate();

        Pesee::create([
            'lapin_id' => $this->lapin->id,
            'date_pesee' => $this->nouvellePeseeDate,
            'poids_g' => $this->nouveauPoidsG,
        ]);

        $this->nouveauPoidsG = null;
        $this->lapin->refresh();

        $this->flash('Pesée enregistrée.');
    }

    public function render()
    {
        $this->lapin->load(['race', 'cage', 'pere', 'mere']);
        $pesees = $this->lapin->pesees()->get();

        return view('livewire.lapins.show', [
            'pesees' => $pesees,
            'poidsLabels' => $pesees->map(fn (Pesee $p) => $p->date_pesee->format('d/m/Y'))->all(),
            'poidsData' => $pesees->pluck('poids_g')->all(),
            'santeInterventions' => $this->lapin->santeInterventions()->get(),
            'sorties' => $this->lapin->sorties()->orderByDesc('date')->get(),
            'descendants' => Lapin::where('pere_id', $this->lapin->id)
                ->orWhere('mere_id', $this->lapin->id)
                ->orderByDesc('date_naissance')
                ->get(),
        ]);
    }
}
