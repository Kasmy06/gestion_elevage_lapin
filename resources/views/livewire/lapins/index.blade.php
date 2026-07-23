<div>
    <x-page-header title="Cheptel" subtitle="Tous les lapins de l'élevage">
        <x-slot name="actions">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('lapins.corbeille') }}" wire:navigate>
                    <x-secondary-button>
                        <span class="material-icons text-base">delete_outline</span> Corbeille
                    </x-secondary-button>
                </a>
            @endif
            <a href="{{ route('lapins.create') }}" wire:navigate>
                <x-primary-button>
                    <span class="material-icons text-base">add</span> Ajouter un lapin
                </x-primary-button>
            </a>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <div class="mb-4 flex flex-wrap gap-3 rounded-xl bg-farm-bg p-3">
        <div class="min-w-[160px] flex-1">
            <x-text-input wire:model.live.debounce.400ms="search" id="search" class="block w-full text-sm" placeholder="🔍 Identifiant..." />
        </div>
        <x-select-input wire:model.live="sexe" id="sexe" class="text-sm">
            <option value="">Tous sexes</option>
            @foreach (\App\Models\Lapin::SEXES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-select-input>
        <x-select-input wire:model.live="statut" id="statut" class="text-sm">
            <option value="">Tous statuts</option>
            @foreach (\App\Models\Lapin::STATUTS as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-select-input>
        <x-select-input wire:model.live="raceId" id="raceId" class="text-sm">
            <option value="">Toutes races</option>
            @foreach ($races as $race)
                <option value="{{ $race->id }}">{{ $race->nom }}</option>
            @endforeach
        </x-select-input>
    </div>

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Identifiant</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Race</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Sexe</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Âge</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Clapier</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Poids</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Statut</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($lapins as $lapin)
                    <tr wire:key="lapin-{{ $lapin->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3">
                            <a href="{{ route('lapins.show', $lapin) }}" wire:navigate class="flex items-center gap-2 font-medium text-farm-green hover:underline">
                                @if ($lapin->photo_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($lapin->photo_path) }}" class="h-8 w-8 rounded-lg object-cover" alt="Photo de {{ $lapin->identifiant }}">
                                @else
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-farm-green-pale text-base">🐇</span>
                                @endif
                                {{ $lapin->identifiant }}
                            </a>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $lapin->race?->nom ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $lapin->sexe ? \App\Models\Lapin::SEXES[$lapin->sexe] : 'Non sexé' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $lapin->age_lisible ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $lapin->cage?->numero ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $lapin->poids_actuel_g ? number_format($lapin->poids_actuel_g / 1000, 2).' kg' : '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="$lapin->statutCouleur()">{{ \App\Models\Lapin::STATUTS[$lapin->statut] }}</x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <a href="{{ route('lapins.edit', $lapin) }}" wire:navigate class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-farm-blue text-white" title="Modifier">
                                <span class="material-icons text-sm">edit</span>
                            </a>
                            @if (auth()->user()->isAdmin())
                                <button
                                    type="button"
                                    wire:click="supprimer({{ $lapin->id }})"
                                    wire:confirm="Supprimer définitivement le lapin {{ $lapin->identifiant }} ?"
                                    class="ms-1 inline-flex h-7 w-7 items-center justify-center rounded-md bg-farm-red text-white"
                                    title="Supprimer"
                                >
                                    <span class="material-icons text-sm">delete</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-farm-text-light">
                            Aucun lapin ne correspond à ces critères.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $lapins->links() }}
    </div>
</div>
