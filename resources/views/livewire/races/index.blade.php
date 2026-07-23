<div>
    <x-page-header title="Races" subtitle="Races d'agrément, à fourrure ou à chair (chapitre 2 de l'Agrodok)">
        <x-slot name="actions">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('races.corbeille') }}" wire:navigate>
                    <x-secondary-button>
                        <span class="material-icons text-base">delete_outline</span> Corbeille
                    </x-secondary-button>
                </a>
            @endif
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Ajouter une race
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Nom</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Catégorie</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Poids</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Lapins</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($races as $race)
                    <tr wire:key="race-{{ $race->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $race->nom }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ \App\Models\Race::CATEGORIES[$race->categorie] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">
                            @if ($race->poids_min_kg || $race->poids_max_kg)
                                {{ $race->poids_min_kg }} – {{ $race->poids_max_kg }} kg
                            @else
                                —
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $race->lapins_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="modifier({{ $race->id }})" class="font-medium text-farm-blue hover:underline">Modifier</button>
                            @if (auth()->user()->isAdmin())
                                <button
                                    type="button"
                                    wire:click="supprimer({{ $race->id }})"
                                    wire:confirm="Supprimer la race « {{ $race->nom }} » ?"
                                    class="ms-3 font-medium text-farm-red hover:underline"
                                >
                                    Supprimer
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucune race enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $races->links() }}
    </div>

    <x-crud-modal :show="$showModal" :title="$editing ? 'Modifier la race' : 'Ajouter une race'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="nom" value="Nom" />
                <x-text-input wire:model="nom" id="nom" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="categorie" value="Catégorie" />
                <x-select-input wire:model="categorie" id="categorie" class="mt-1 block w-full">
                    @foreach (\App\Models\Race::CATEGORIES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('categorie')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="poids_min_kg" value="Poids min (kg)" />
                    <x-text-input wire:model="poids_min_kg" id="poids_min_kg" type="number" step="0.1" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('poids_min_kg')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="poids_max_kg" value="Poids max (kg)" />
                    <x-text-input wire:model="poids_max_kg" id="poids_max_kg" type="number" step="0.1" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('poids_max_kg')" class="mt-2" />
                </div>
            </div>
            <div>
                <x-input-label for="description" value="Description" />
                <x-textarea-input wire:model="description" id="description" rows="3" class="mt-1 block w-full"></x-textarea-input>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
</div>
