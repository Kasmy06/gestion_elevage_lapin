<div class="sticky top-0 z-20 flex h-[60px] items-center gap-4 border-b border-farm-border bg-white px-4 shadow-sm lg:px-6">
    <button
        type="button"
        @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))"
        class="flex h-9 w-9 items-center justify-center rounded-full bg-farm-bg text-farm-text hover:bg-farm-green-pale lg:hidden"
    >
        <span class="material-icons text-xl">menu</span>
    </button>

    <form method="GET" action="{{ route('lapins.index') }}" class="hidden max-w-xs flex-1 sm:block">
        <div class="relative">
            <span class="material-icons pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-lg text-farm-text-light">search</span>
            <input
                type="text"
                name="search"
                placeholder="Rechercher un lapin..."
                class="w-full rounded-full border border-farm-border bg-farm-bg py-1.5 pl-9 pr-3 text-sm text-farm-text placeholder:text-farm-text-light focus:border-farm-green focus:bg-white focus:outline-none focus:ring-1 focus:ring-farm-green"
            >
        </div>
    </form>

    <div class="ms-auto flex items-center gap-2">
        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            title="Alertes"
            class="relative flex h-9 w-9 items-center justify-center rounded-full bg-farm-bg text-farm-text hover:bg-farm-green-pale"
        >
            <span class="material-icons text-xl">notifications</span>
            @if ($alertesCount > 0)
                <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full border-2 border-white bg-farm-red"></span>
            @endif
        </a>

        <x-dropdown align="right" width="56">
            <x-slot name="trigger">
                <button class="flex h-9 w-9 items-center justify-center rounded-full bg-farm-green text-sm font-semibold text-white">
                    {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="border-b border-farm-border px-4 py-3">
                    <p class="truncate text-sm font-medium text-farm-text">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-farm-text-light">{{ auth()->user()->isAdmin() ? 'Administrateur' : 'Éleveur' }}</p>
                </div>
                <x-dropdown-link :href="route('profile')" wire:navigate>
                    Profil
                </x-dropdown-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-dropdown-link>
                        Déconnexion
                    </x-dropdown-link>
                </button>
            </x-slot>
        </x-dropdown>
    </div>
</div>
