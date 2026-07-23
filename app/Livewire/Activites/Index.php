<?php

namespace App\Livewire\Activites;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $userId = '';

    #[Url(history: true)]
    public string $module = '';

    #[Url(history: true)]
    public string $action = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function updating($property): void
    {
        if (in_array($property, ['userId', 'module', 'action'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $activites = ActivityLog::query()
            ->with('user')
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId))
            ->when($this->module, fn ($q) => $q->where('subject_type', $this->module))
            ->when($this->action, fn ($q) => $q->where('action', $this->action))
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('livewire.activites.index', [
            'activites' => $activites,
            'utilisateurs' => User::orderBy('name')->get(),
        ]);
    }
}
