<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-xl font-bold text-farm-text">Connexion</h2>
    <p class="mb-6 mt-1 text-sm text-farm-text-light">Bienvenue ! Connectez-vous pour accéder à votre ferme.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Adresse email" />
            <x-text-input wire:model="form.email" id="email" class="mt-1 block w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input wire:model="form.password" id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <label for="remember" class="inline-flex items-center">
            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-farm-border text-farm-green shadow-sm focus:ring-farm-green" name="remember">
            <span class="ms-2 text-sm text-farm-text-light">Se souvenir de moi</span>
        </label>

        <button type="submit" class="w-full rounded-lg bg-farm-green px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-farm-green-dark">
            Se connecter
        </button>

        @if (Route::has('password.request'))
            <p class="text-center text-sm text-farm-text-light">
                <a class="font-medium text-farm-green hover:underline" href="{{ route('password.request') }}" wire:navigate>
                    Mot de passe oublié ?
                </a>
            </p>
        @endif
    </form>
</div>
