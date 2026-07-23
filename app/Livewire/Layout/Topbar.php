<?php

namespace App\Livewire\Layout;

use App\Livewire\Actions\Logout;
use App\Support\AlertesElevage;
use Livewire\Component;

class Topbar extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.layout.topbar', [
            'alertesCount' => AlertesElevage::total(),
        ]);
    }
}
