<div>
    <x-page-header title="Alimentation" subtitle="Journal des entrées et distributions">
        <x-slot name="actions">
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Nouveau mouvement
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-alimentation-tabs active="mouvements" />

    <x-flash :message="$flashMessage" :type="$flashType" />

    <div class="mb-4 flex flex-wrap gap-3 rounded-xl bg-farm-bg p-3">
        <x-select-input wire:model.live="cageFiltre" id="cageFiltre" class="text-sm">
            <option value="">Tous les clapiers</option>
            @foreach ($cages as $cage)
                <option value="{{ $cage->id }}">{{ $cage->numero }}</option>
            @endforeach
        </x-select-input>
    </div>

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Date</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Aliment</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Mouvement</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Quantité</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Clapier</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Coût</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($mouvements as $mouvement)
                    <tr wire:key="mouvement-{{ $mouvement->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $mouvement->date->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $mouvement->aliment?->nom ?? '#'.$mouvement->aliment_id.' (supprimé)' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="$mouvement->type_mouvement === 'entree' ? 'green' : 'blue'">
                                {{ \App\Models\MouvementAliment::TYPES_MOUVEMENT[$mouvement->type_mouvement] }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ rtrim(rtrim($mouvement->quantite, '0'), '.') }} {{ $mouvement->aliment?->unite }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $mouvement->cage?->numero ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $mouvement->cout ? number_format($mouvement->cout, 0, ',', ' ').' '.\App\Models\Parametre::current()->devise : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucun mouvement enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $mouvements->links() }}
    </div>

    <x-crud-modal :show="$showModal" title="Nouveau mouvement de stock">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="aliment_id" value="Aliment" />
                <x-select-input wire:model="aliment_id" id="aliment_id" class="mt-1 block w-full">
                    <option value="">Sélectionner...</option>
                    @foreach ($aliments as $aliment)
                        <option value="{{ $aliment->id }}">{{ $aliment->nom }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('aliment_id')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="type_mouvement" value="Type" />
                    <x-select-input wire:model="type_mouvement" id="type_mouvement" class="mt-1 block w-full">
                        @foreach (\App\Models\MouvementAliment::TYPES_MOUVEMENT as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('type_mouvement')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date" value="Date" />
                    <x-text-input wire:model="date" id="date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="quantite" value="Quantité" />
                    <x-text-input wire:model="quantite" id="quantite" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('quantite')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="cout" :value="'Coût ('.\App\Models\Parametre::current()->devise.', optionnel)'" />
                    <x-text-input wire:model="cout" id="cout" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('cout')" class="mt-2" />
                </div>
            </div>
            <div>
                <x-input-label for="cage_id" value="Clapier concerné (optionnel)" />
                <x-select-input wire:model="cage_id" id="cage_id" class="mt-1 block w-full">
                    <option value="">—</option>
                    @foreach ($cages as $cage)
                        <option value="{{ $cage->id }}">{{ $cage->numero }}</option>
                    @endforeach
                </x-select-input>
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
