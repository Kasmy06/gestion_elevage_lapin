<?php

namespace App\Livewire\Lapins;

use App\Models\Cage;
use App\Models\Lapin;
use App\Models\Race;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public ?Lapin $lapin = null;

    public string $identifiant = '';

    public ?int $race_id = null;

    public ?string $sexe = null;

    public ?string $date_naissance = null;

    public ?int $pere_id = null;

    public ?int $mere_id = null;

    public ?int $cage_id = null;

    public string $statut = 'jeune';

    public string $origine = 'naissance_elevage';

    public ?int $poids_actuel_g = null;

    public ?string $date_acquisition = null;

    public ?string $notes = null;

    public $photo = null;

    public function mount(?Lapin $lapin = null): void
    {
        if ($lapin?->exists) {
            $this->lapin = $lapin;
            $this->identifiant = $lapin->identifiant;
            $this->race_id = $lapin->race_id;
            $this->sexe = $lapin->sexe;
            $this->date_naissance = $lapin->date_naissance?->toDateString();
            $this->pere_id = $lapin->pere_id;
            $this->mere_id = $lapin->mere_id;
            $this->cage_id = $lapin->cage_id;
            $this->statut = $lapin->statut;
            $this->origine = $lapin->origine;
            $this->poids_actuel_g = $lapin->poids_actuel_g;
            $this->date_acquisition = $lapin->date_acquisition?->toDateString();
            $this->notes = $lapin->notes;
        }
    }

    protected function rules(): array
    {
        return [
            'identifiant' => [
                'required', 'string', 'max:50',
                Rule::unique('lapins', 'identifiant')->ignore($this->lapin?->id),
            ],
            'race_id' => ['nullable', Rule::exists('races', 'id')],
            'sexe' => ['nullable', Rule::in(array_keys(Lapin::SEXES))],
            'date_naissance' => ['nullable', 'date', 'before_or_equal:today'],
            'pere_id' => ['nullable', Rule::exists('lapins', 'id'), 'different:mere_id'],
            'mere_id' => ['nullable', Rule::exists('lapins', 'id')],
            'cage_id' => ['nullable', Rule::exists('cages', 'id')],
            'statut' => ['required', Rule::in(array_keys(Lapin::STATUTS))],
            'origine' => ['required', Rule::in(array_keys(Lapin::ORIGINES))],
            'poids_actuel_g' => ['nullable', 'integer', 'min:0'],
            'date_acquisition' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'identifiant.required' => "L'identifiant (numéro de boucle) est obligatoire.",
            'identifiant.unique' => 'Cet identifiant est déjà utilisé par un autre lapin.',
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'pere_id.different' => 'Le père et la mère doivent être deux lapins différents.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->cage_id) {
            $cage = Cage::find($this->cage_id);
            $occupation = $cage->lapins()->when($this->lapin, fn ($q) => $q->whereKeyNot($this->lapin->id))->count();

            if ($occupation >= $cage->capacite) {
                $this->addError('cage_id', "Le clapier {$cage->numero} est déjà complet ({$cage->capacite} place(s)).");

                return;
            }
        }

        $data = [
            'identifiant' => $this->identifiant,
            'race_id' => $this->race_id,
            'sexe' => $this->sexe,
            'date_naissance' => $this->date_naissance,
            'pere_id' => $this->pere_id,
            'mere_id' => $this->mere_id,
            'cage_id' => $this->cage_id,
            'statut' => $this->statut,
            'origine' => $this->origine,
            'poids_actuel_g' => $this->poids_actuel_g,
            'date_acquisition' => $this->date_acquisition,
            'notes' => $this->notes,
        ];

        if ($this->photo) {
            $data['photo_path'] = $this->photo->store('lapins', 'public');
        }

        if ($this->lapin) {
            $this->lapin->update($data);
            $lapin = $this->lapin;
            $message = 'Le lapin '.$lapin->identifiant.' a été mis à jour.';
        } else {
            $lapin = Lapin::create($data);
            $message = 'Le lapin '.$lapin->identifiant.' a été ajouté.';
        }

        session()->flash('success', $message);

        $this->redirect(route('lapins.show', $lapin), navigate: true);
    }

    public function render()
    {
        return view('livewire.lapins.form', [
            'races' => Race::orderBy('nom')->get(),
            'cages' => Cage::orderBy('numero')->get(),
            'peres' => Lapin::disponiblesPourSaillie('male')->when($this->lapin, fn ($q) => $q->whereKeyNot($this->lapin->id))->orderBy('identifiant')->get(),
            'meres' => Lapin::disponiblesPourSaillie('femelle')->when($this->lapin, fn ($q) => $q->whereKeyNot($this->lapin->id))->orderBy('identifiant')->get(),
        ]);
    }
}
