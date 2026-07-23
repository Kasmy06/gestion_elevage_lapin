<div>
    <x-page-header title="Clapiers" subtitle="Logement du cheptel (chapitres 5 et 6 de l'Agrodok)">
        <x-slot name="actions">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('cages.corbeille') }}" wire:navigate>
                    <x-secondary-button>
                        <span class="material-icons text-base">delete_outline</span> Corbeille
                    </x-secondary-button>
                </a>
            @endif
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Ajouter un clapier
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Numéro</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Type</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Emplacement</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Occupation</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($cages as $cage)
                    <tr wire:key="cage-{{ $cage->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $cage->numero }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ \App\Models\Cage::TYPES[$cage->type] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $cage->emplacement ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="$cage->lapins_count >= $cage->capacite ? 'red' : 'green'">
                                {{ $cage->lapins_count }} / {{ $cage->capacite }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="modifier({{ $cage->id }})" class="font-medium text-farm-blue hover:underline">Modifier</button>
                            @if (auth()->user()->isAdmin())
                                <button
                                    type="button"
                                    wire:click="supprimer({{ $cage->id }})"
                                    wire:confirm="Supprimer le clapier {{ $cage->numero }} ?"
                                    class="ms-3 font-medium text-farm-red hover:underline"
                                >
                                    Supprimer
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucun clapier enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $cages->links() }}
    </div>

    <x-crud-modal :show="$showModal" :title="$editing ? 'Modifier le clapier' : 'Ajouter un clapier'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="numero" value="Numéro" />
                <x-text-input wire:model="numero" id="numero" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="type" value="Type" />
                <x-select-input wire:model="type" id="type" class="mt-1 block w-full">
                    @foreach (\App\Models\Cage::TYPES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="emplacement" value="Emplacement" />
                <x-text-input wire:model="emplacement" id="emplacement" class="mt-1 block w-full" placeholder="Étable A..." />
                <x-input-error :messages="$errors->get('emplacement')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="capacite" value="Capacité (nombre de lapins)" />
                <x-text-input wire:model="capacite" id="capacite" type="number" min="1" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('capacite')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="notes" value="Notes" />
                <x-textarea-input wire:model="notes" id="notes" rows="2" class="mt-1 block w-full"></x-textarea-input>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
</div>
