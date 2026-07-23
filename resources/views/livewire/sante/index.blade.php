<div>
    <x-page-header title="Santé" subtitle="Prévention et suivi des maladies (chapitre 8 de l'Agrodok)">
        <x-slot name="actions">
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Nouveau suivi
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <div class="mb-4 max-w-xs">
        <x-select-input wire:model.live="statutFiltre" id="statutFiltre" class="w-full text-sm">
            <option value="">Tous statuts</option>
            @foreach (\App\Models\SanteIntervention::STATUTS as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-select-input>
    </div>

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Lapin</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Type</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Libellé</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Depuis le</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Statut</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($interventions as $intervention)
                    <tr wire:key="intervention-{{ $intervention->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3">
                            @if ($intervention->lapin)
                                <a href="{{ route('lapins.show', $intervention->lapin) }}" wire:navigate class="font-medium text-farm-green hover:underline">
                                    {{ $intervention->lapin->identifiant }}
                                </a>
                            @else
                                <span class="text-farm-text-light">#{{ $intervention->lapin_id }} (supprimé)</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ \App\Models\SanteIntervention::TYPES[$intervention->type] }}</td>
                        <td class="px-4 py-3 text-sm text-farm-text-light">{{ $intervention->libelle }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $intervention->date_debut->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="$intervention->statut === 'gueri' ? 'green' : ($intervention->statut === 'deces' ? 'red' : 'orange')">
                                {{ \App\Models\SanteIntervention::STATUTS[$intervention->statut] }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            @if ($intervention->statut === 'en_cours')
                                <button type="button" wire:click="cloturer({{ $intervention->id }}, 'gueri')" class="font-medium text-farm-green hover:underline">
                                    Guéri
                                </button>
                                <button
                                    type="button"
                                    wire:click="cloturer({{ $intervention->id }}, 'deces')"
                                    wire:confirm="Confirmer le décès de {{ $intervention->lapin?->identifiant ?? '#'.$intervention->lapin_id }} ?"
                                    class="ms-3 font-medium text-farm-red hover:underline"
                                >
                                    Décès
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucun suivi santé enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $interventions->links() }}
    </div>

    <x-crud-modal :show="$showModal" title="Nouveau suivi santé">
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
                    <x-input-label for="type" value="Type" />
                    <x-select-input wire:model="type" id="type" class="mt-1 block w-full">
                        @foreach (\App\Models\SanteIntervention::TYPES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date_debut" value="Date de constat" />
                    <x-text-input wire:model="date_debut" id="date_debut" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                </div>
            </div>
            <div>
                <x-input-label for="libelle" value="Libellé" />
                <x-text-input wire:model="libelle" id="libelle" class="mt-1 block w-full" placeholder="Coccidiose, gale des oreilles..." />
                <x-input-error :messages="$errors->get('libelle')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="traitement_applique" value="Traitement appliqué" />
                <x-textarea-input wire:model="traitement_applique" id="traitement_applique" rows="2" class="mt-1 block w-full"></x-textarea-input>
                <x-input-error :messages="$errors->get('traitement_applique')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="cout" :value="'Coût ('.\App\Models\Parametre::current()->devise.', optionnel)'" />
                <x-text-input wire:model="cout" id="cout" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('cout')" class="mt-2" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
</div>
