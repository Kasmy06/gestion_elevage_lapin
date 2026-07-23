<div>
    <x-page-header title="Sorties" subtitle="Ventes, abattages, mortalité et dons (chapitre 10 de l'Agrodok)">
        <x-slot name="actions">
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Nouvelle sortie
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Date</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Lapin</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Type</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Poids</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Prix</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Acheteur / cause</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($sorties as $sortie)
                    <tr wire:key="sortie-{{ $sortie->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $sortie->date->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if ($sortie->lapin)
                                <a href="{{ route('lapins.show', $sortie->lapin) }}" wire:navigate class="font-medium text-farm-green hover:underline">
                                    {{ $sortie->lapin->identifiant }}
                                </a>
                            @else
                                <span class="text-farm-text-light">#{{ $sortie->lapin_id }} (supprimé)</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="match($sortie->type) { 'vente' => 'green', 'abattage' => 'blue', 'mort' => 'red', default => 'purple' }">
                                {{ \App\Models\Sortie::TYPES[$sortie->type] }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $sortie->poids_g ? number_format($sortie->poids_g / 1000, 2).' kg' : '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $sortie->prix ? number_format($sortie->prix, 0, ',', ' ').' '.\App\Models\Parametre::current()->devise : '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $sortie->acheteur ?? $sortie->cause ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucune sortie enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $sorties->links() }}
    </div>

    <x-crud-modal :show="$showModal" title="Nouvelle sortie">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="lapin_id" value="Lapin" />
                <x-select-input wire:model="lapin_id" id="lapin_id" class="mt-1 block w-full">
                    <option value="">Sélectionner...</option>
                    @foreach ($lapins as $lapin)
                        <option value="{{ $lapin->id }}">{{ $lapin->identifiant }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('lapin_id')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="type" value="Type de sortie" />
                    <x-select-input wire:model.live="type" id="type" class="mt-1 block w-full">
                        @foreach (\App\Models\Sortie::TYPES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date" value="Date" />
                    <x-text-input wire:model="date" id="date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                </div>
            </div>

            @if (in_array($type, ['vente', 'abattage']))
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="poids_g" value="Poids (g)" />
                        <x-text-input wire:model="poids_g" id="poids_g" type="number" min="0" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('poids_g')" class="mt-2" />
                    </div>
                    @if ($type === 'vente')
                        <div>
                            <x-input-label for="prix" :value="'Prix ('.\App\Models\Parametre::current()->devise.')'" />
                            <x-text-input wire:model="prix" id="prix" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('prix')" class="mt-2" />
                        </div>
                    @endif
                </div>
            @endif

            @if ($type === 'vente')
                <div>
                    <x-input-label for="acheteur" value="Acheteur" />
                    <x-text-input wire:model="acheteur" id="acheteur" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('acheteur')" class="mt-2" />
                </div>
            @endif

            @if ($type === 'mort')
                <div>
                    <x-input-label for="cause" value="Cause présumée" />
                    <x-text-input wire:model="cause" id="cause" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('cause')" class="mt-2" />
                </div>
            @endif

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
