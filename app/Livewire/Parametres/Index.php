<?php

namespace App\Livewire\Parametres;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\Parametre;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage;

    public string $nom_ferme = '';

    public string $devise = '';

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
        ]);

        Parametre::current()->update([
            'nom_ferme' => $this->nom_ferme,
            'devise' => $this->devise,
        ]);

        $this->flash('Paramètres enregistrés.');
    }

    public function render()
    {
        return view('livewire.parametres.index');
    }
}
