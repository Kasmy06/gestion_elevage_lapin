<?php

namespace App\Livewire\Parametres;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Parametre;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithFileUploads;

    public string $nom_ferme = '';

    public string $devise = '';

    public $logo = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $parametre = Parametre::current();
        $this->nom_ferme = $parametre->nom_ferme;
        $this->devise = $parametre->devise;
    }

    public function save(): void
    {
        $this->validate([
            'nom_ferme' => ['required', 'string', 'max:150'],
            'devise' => ['required', 'string', 'max:10'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'nom_ferme' => $this->nom_ferme,
            'devise' => $this->devise,
        ];

        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('logo', 'public');
        }

        Parametre::current()->update($data);

        $this->reset('logo');

        $this->flash('Paramètres enregistrés.');
    }

    public function render()
    {
        return view('livewire.parametres.index', [
            'parametre' => Parametre::current(),
        ]);
    }
}
