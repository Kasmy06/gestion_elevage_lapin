<div>
    <x-page-header title="Paramètres" subtitle="Configuration générale de l'application" />

    <x-flash :message="$flashMessage" :type="$flashType" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[220px_1fr]">
        <div class="rounded-xl border border-farm-border bg-white p-2 shadow-sm">
            <div class="flex items-center gap-2 rounded-lg bg-farm-green-pale px-3.5 py-2.5 text-sm font-medium text-farm-green">
                <span class="material-icons text-lg">tune</span> Général
            </div>
            <a href="{{ route('profile') }}" wire:navigate class="mt-1 flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-sm text-farm-text-light hover:bg-farm-bg">
                <span class="material-icons text-lg">person</span> Mon profil
            </a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('utilisateurs.index') }}" wire:navigate class="mt-1 flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-sm text-farm-text-light hover:bg-farm-bg">
                    <span class="material-icons text-lg">group</span> Utilisateurs
                </a>
            @endif
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
            <h3 class="mb-4 border-b border-farm-border pb-2 text-sm font-semibold text-farm-text">Informations de la ferme</h3>
            <form wire:submit="save" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="nom_ferme" value="Nom de la ferme" />
                    <x-text-input wire:model="nom_ferme" id="nom_ferme" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('nom_ferme')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="devise" value="Devise" />
                    <x-text-input wire:model="devise" id="devise" class="mt-1 block w-full" placeholder="FCFA" />
                    <x-input-error :messages="$errors->get('devise')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-primary-button>Enregistrer</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
