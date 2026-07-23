<div>
    <x-page-header title="Ventes" subtitle="Registre commercial (lapins, peaux, fumier...)">
        <x-slot name="actions">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('ventes.corbeille') }}" wire:navigate>
                    <x-secondary-button>
                        <span class="material-icons text-base">delete_outline</span> Corbeille
                    </x-secondary-button>
                </a>
            @endif
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Nouvelle vente
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <div class="mb-5 grid grid-cols-1 sm:grid-cols-3">
        <x-stat-card label="Ventes ce mois-ci" :value="number_format($totalPeriode, 0, ',', ' ').' '.\App\Models\Parametre::current()->devise" icon="point_of_sale" color="green" />
    </div>

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Date</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Description</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Client</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Qté × P.U.</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Total</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Paiement</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($ventes as $vente)
                    <tr wire:key="vente-{{ $vente->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $vente->date->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $vente->description }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $vente->client?->nom ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ rtrim(rtrim($vente->quantite, '0'), '.') }} × {{ number_format($vente->prix_unitaire, 0, ',', ' ') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-farm-text">{{ number_format($vente->montant_total, 0, ',', ' ') }} {{ \App\Models\Parametre::current()->devise }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="match($vente->statut_paiement) { 'paye' => 'green', 'partiel' => 'orange', default => 'red' }">
                                {{ \App\Models\Vente::STATUTS_PAIEMENT[$vente->statut_paiement] }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            @if (auth()->user()->isAdmin())
                                <button
                                    type="button"
                                    wire:click="supprimer({{ $vente->id }})"
                                    wire:confirm="Supprimer cette vente ?"
                                    class="font-medium text-farm-red hover:underline"
                                >
                                    Supprimer
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucune vente enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $ventes->links() }}
    </div>

    <x-crud-modal :show="$showModal" title="Nouvelle vente">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="description" value="Description" />
                <x-text-input wire:model="description" id="description" class="mt-1 block w-full" placeholder="Lapin adulte, peau tannée, fumier..." />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="client_id" value="Client (optionnel)" />
                <x-select-input wire:model="client_id" id="client_id" class="mt-1 block w-full">
                    <option value="">—</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->nom }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label for="quantite" value="Quantité" />
                    <x-text-input wire:model="quantite" id="quantite" type="number" step="0.01" min="0.01" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('quantite')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="prix_unitaire" value="Prix unitaire" />
                    <x-text-input wire:model="prix_unitaire" id="prix_unitaire" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('prix_unitaire')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date" value="Date" />
                    <x-text-input wire:model="date" id="date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="mode_paiement" value="Mode de paiement" />
                    <x-select-input wire:model="mode_paiement" id="mode_paiement" class="mt-1 block w-full">
                        @foreach (\App\Models\Vente::MODES_PAIEMENT as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="statut_paiement" value="Statut du paiement" />
                    <x-select-input wire:model="statut_paiement" id="statut_paiement" class="mt-1 block w-full">
                        @foreach (\App\Models\Vente::STATUTS_PAIEMENT as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
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
