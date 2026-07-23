<?php

namespace App\Livewire\Utilisateurs;

use App\Livewire\Concerns\HasFlashMessage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasFlashMessage, WithPagination;

    public bool $showModal = false;

    public ?User $editing = null;

    public string $name = '';

    public string $email = '';

    public string $role = 'eleveur';

    public ?string $password = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function creer(): void
    {
        $this->reset(['editing', 'name', 'email', 'password']);
        $this->role = 'eleveur';
        $this->showModal = true;
    }

    public function modifier(User $user): void
    {
        $this->editing = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = null;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editing?->id)],
            'role' => ['required', 'in:admin,eleveur'],
            'password' => [$this->editing ? 'nullable' : 'required', 'min:8'],
        ]);

        if ($this->editing && $this->editing->isAdmin() && $this->role !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            $this->flash('Impossible de rétrograder le dernier administrateur.', 'error');

            return;
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editing) {
            $this->editing->update($data);
            $this->flash('Utilisateur '.$this->name.' mis à jour.');
        } else {
            $data['email_verified_at'] = now();
            User::create($data);
            $this->flash('Utilisateur '.$this->name.' créé.');
        }

        $this->closeModal();
    }

    public function supprimer(User $user): void
    {
        if ($user->id === auth()->id()) {
            $this->flash('Vous ne pouvez pas supprimer votre propre compte.', 'error');

            return;
        }

        $user->delete();
        $this->flash('Utilisateur supprimé.');
    }

    public function render()
    {
        return view('livewire.utilisateurs.index', [
            'users' => User::orderBy('name')->paginate(15),
        ]);
    }
}
