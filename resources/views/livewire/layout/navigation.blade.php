<div x-data="{ sidebarOpen: false }" x-on:toggle-sidebar.window="sidebarOpen = ! sidebarOpen" class="lg:flex lg:shrink-0">
    <!-- Fond assombri (mobile) -->
    <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"></div>

    <!-- Panneau latéral -->
    <aside
        x-cloak
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full transform flex-col overflow-y-auto bg-farm-green text-white transition-transform duration-200 ease-in-out lg:static lg:z-auto lg:translate-x-0"
    >
        <div class="flex items-center gap-3 border-b border-white/15 px-4 py-5">
            <img src="{{ \App\Models\Parametre::current()->logoUrl() }}" alt="{{ config('app.name') }}" class="h-9 w-9 shrink-0 rounded-full object-cover">
            <div class="min-w-0">
                <h1 class="truncate text-[15px] font-semibold leading-tight">{{ config('app.name') }}</h1>
                <span class="text-[11px] font-light text-white/70">Gestion d'élevage cunicole</span>
            </div>
            <button @click="sidebarOpen = false" class="ms-auto flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 text-white lg:hidden">
                <span class="material-icons text-lg">chevron_left</span>
            </button>
        </div>

        <nav class="flex-1 space-y-5 overflow-y-auto py-3">
            <div class="space-y-1 px-3">
                <x-sidebar-link icon="dashboard" :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    Tableau de bord
                </x-sidebar-link>
            </div>

            <div>
                <p class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-white/50">Élevage</p>
                <div class="space-y-1 px-3">
                    <x-sidebar-link icon="pets" :href="route('lapins.index')" :active="request()->routeIs('lapins.*')" wire:navigate>
                        Cheptel
                    </x-sidebar-link>
                    <x-sidebar-link icon="favorite" :href="route('reproduction.saillies.index')" :active="request()->routeIs('reproduction.saillies.*')" wire:navigate>
                        Reproduction
                    </x-sidebar-link>
                    <x-sidebar-link icon="child_care" :href="route('reproduction.mises-bas.index')" :active="request()->routeIs('reproduction.mises-bas.*')" wire:navigate>
                        Maternité
                    </x-sidebar-link>
                    <x-sidebar-link icon="restaurant" :href="route('alimentation.aliments.index')" :active="request()->routeIs('alimentation.*')" wire:navigate>
                        Alimentation
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-white/50">Infrastructure</p>
                <div class="space-y-1 px-3">
                    <x-sidebar-link icon="home" :href="route('cages.index')" :active="request()->routeIs('cages.*')" wire:navigate>
                        Clapiers
                    </x-sidebar-link>
                    <x-sidebar-link icon="category" :href="route('races.index')" :active="request()->routeIs('races.*')" wire:navigate>
                        Races
                    </x-sidebar-link>
                    <x-sidebar-link icon="local_hospital" :href="route('sante.index')" :active="request()->routeIs('sante.*')" wire:navigate>
                        Santé
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-white/50">Finance &amp; ventes</p>
                <div class="space-y-1 px-3">
                    <x-sidebar-link icon="account_balance" :href="route('comptabilite.index')" :active="request()->routeIs('comptabilite.*')" wire:navigate>
                        Comptabilité
                    </x-sidebar-link>
                    <x-sidebar-link icon="point_of_sale" :href="route('ventes.index')" :active="request()->routeIs('ventes.*')" wire:navigate>
                        Ventes
                    </x-sidebar-link>
                    <x-sidebar-link icon="people" :href="route('clients.index')" :active="request()->routeIs('clients.*')" wire:navigate>
                        Clients
                    </x-sidebar-link>
                    <x-sidebar-link icon="output" :href="route('sorties.index')" :active="request()->routeIs('sorties.*')" wire:navigate>
                        Sorties
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-white/50">Analyse</p>
                <div class="space-y-1 px-3">
                    <x-sidebar-link icon="bar_chart" :href="route('rapports.index')" :active="request()->routeIs('rapports.*')" wire:navigate>
                        Rapports
                    </x-sidebar-link>
                </div>
            </div>

            @if (auth()->user()->isAdmin())
                <div>
                    <p class="px-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-white/50">Administration</p>
                    <div class="space-y-1 px-3">
                        <x-sidebar-link icon="settings" :href="route('parametres.index')" :active="request()->routeIs('parametres.*')" wire:navigate>
                            Paramètres
                        </x-sidebar-link>
                        <x-sidebar-link icon="admin_panel_settings" :href="route('utilisateurs.index')" :active="request()->routeIs('utilisateurs.*')" wire:navigate>
                            Utilisateurs
                        </x-sidebar-link>
                        <x-sidebar-link icon="badge" :href="route('employes.index')" :active="request()->routeIs('employes.*')" wire:navigate>
                            Employés
                        </x-sidebar-link>
                        <x-sidebar-link icon="history" :href="route('activites.index')" :active="request()->routeIs('activites.*')" wire:navigate>
                            Journal d'activité
                        </x-sidebar-link>
                    </div>
                </div>
            @endif
        </nav>
    </aside>
</div>
