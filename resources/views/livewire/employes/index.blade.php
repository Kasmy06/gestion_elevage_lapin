<div>
    <x-page-header title="Employés" subtitle="Personnel de la ferme">
        <x-slot name="actions">
            <a href="{{ route('employes.corbeille') }}" wire:navigate>
                <x-secondary-button>
                    <span class="material-icons text-base">delete_outline</span> Corbeille
                </x-secondary-button>
            </a>
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Ajouter
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Nom</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Poste</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Téléphone</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Embauche</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Statut</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($employes as $employe)
                    <tr wire:key="employe-{{ $employe->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $employe->nom }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $employe->poste }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $employe->telephone ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $employe->date_embauche?->format('d/m/Y') ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="$employe->statut === 'actif' ? 'green' : 'gray'">{{ \App\Models\Employe::STATUTS[$employe->statut] }}</x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="modifier({{ $employe->id }})" class="font-medium text-farm-blue hover:underline">Modifier</button>
                            <button
                                type="button"
                                wire:click="supprimer({{ $employe->id }})"
                                wire:confirm="Supprimer l'employé {{ $employe->nom }} ?"
                                class="ms-3 font-medium text-farm-red hover:underline"
                            >
                                Supprimer
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucun employé enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $employes->links() }}
    </div>

    <x-crud-modal :show="$showModal" :title="$editing ? 'Modifier l\'employé' : 'Ajouter un employé'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="nom" value="Nom complet" />
                <x-text-input wire:model="nom" id="nom" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="poste" value="Poste" />
                    <x-text-input wire:model="poste" id="poste" class="mt-1 block w-full" placeholder="Gardien, Vétérinaire..." />
                    <x-input-error :messages="$errors->get('poste')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="statut" value="Statut" />
                    <x-select-input wire:model="statut" id="statut" class="mt-1 block w-full">
                        @foreach (\App\Models\Employe::STATUTS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="telephone" value="Téléphone" />
                    <x-text-input wire:model="telephone" id="telephone" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="date_embauche" value="Date d'embauche" />
                    <x-text-input wire:model="date_embauche" id="date_embauche" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_embauche')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="salaire" value="Salaire mensuel" />
                    <x-text-input wire:model="salaire" id="salaire" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('salaire')" class="mt-2" />
                </div>
            </div>
            <div>
                <x-input-label for="notes" value="Notes" />
                <x-textarea-input wire:model="notes" id="notes" rows="2" class="mt-1 block w-full"></x-textarea-input>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
</div>
