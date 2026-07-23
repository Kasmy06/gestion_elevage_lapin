<div>
    <x-page-header title="Alimentation" subtitle="Stocks d'aliments (chapitre 7 de l'Agrodok)">
        <x-slot name="actions">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('alimentation.aliments.corbeille') }}" wire:navigate>
                    <x-secondary-button>
                        <span class="material-icons text-base">delete_outline</span> Corbeille
                    </x-secondary-button>
                </a>
            @endif
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Ajouter un aliment
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-alimentation-tabs active="aliments" />

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Nom</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Type</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Stock actuel</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Seuil d'alerte</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($aliments as $aliment)
                    <tr wire:key="aliment-{{ $aliment->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $aliment->nom }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ \App\Models\Aliment::TYPES[$aliment->type] }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="$aliment->stockBas() ? 'red' : 'green'">
                                {{ rtrim(rtrim($aliment->stock_actuel, '0'), '.') }} {{ $aliment->unite }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">
                            {{ $aliment->seuil_alerte ? rtrim(rtrim($aliment->seuil_alerte, '0'), '.').' '.$aliment->unite : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="modifier({{ $aliment->id }})" class="font-medium text-farm-blue hover:underline">Modifier</button>
                            @if (auth()->user()->isAdmin())
                                <button
                                    type="button"
                                    wire:click="supprimer({{ $aliment->id }})"
                                    wire:confirm="Supprimer l'aliment « {{ $aliment->nom }} » et tout son historique de mouvements ?"
                                    class="ms-3 font-medium text-farm-red hover:underline"
                                >
                                    Supprimer
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucun aliment enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $aliments->links() }}
    </div>

    <x-crud-modal :show="$showModal" :title="$editing ? 'Modifier l\'aliment' : 'Ajouter un aliment'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="nom" value="Nom" />
                <x-text-input wire:model="nom" id="nom" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="type" value="Type" />
                    <x-select-input wire:model="type" id="type" class="mt-1 block w-full">
                        @foreach (\App\Models\Aliment::TYPES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="unite" value="Unité" />
                    <x-text-input wire:model="unite" id="unite" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('unite')" class="mt-2" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="stock_actuel" value="Stock actuel" />
                    <x-text-input wire:model="stock_actuel" id="stock_actuel" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('stock_actuel')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="seuil_alerte" value="Seuil d'alerte" />
                    <x-text-input wire:model="seuil_alerte" id="seuil_alerte" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('seuil_alerte')" class="mt-2" />
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
</div>
