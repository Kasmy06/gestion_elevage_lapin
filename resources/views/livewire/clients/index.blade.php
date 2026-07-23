<div>
    <x-page-header title="Clients" subtitle="Acheteurs et débouchés commerciaux">
        <x-slot name="actions">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('clients.corbeille') }}" wire:navigate>
                    <x-secondary-button>
                        <span class="material-icons text-base">delete_outline</span> Corbeille
                    </x-secondary-button>
                </a>
            @endif
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
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Téléphone</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Adresse</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Ventes</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($clients as $client)
                    <tr wire:key="client-{{ $client->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $client->nom }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $client->telephone ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $client->adresse ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $client->ventes_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="modifier({{ $client->id }})" class="font-medium text-farm-blue hover:underline">Modifier</button>
                            @if (auth()->user()->isAdmin())
                                <button
                                    type="button"
                                    wire:click="supprimer({{ $client->id }})"
                                    wire:confirm="Supprimer le client {{ $client->nom }} ?"
                                    class="ms-3 font-medium text-farm-red hover:underline"
                                >
                                    Supprimer
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucun client enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>

    <x-crud-modal :show="$showModal" :title="$editing ? 'Modifier le client' : 'Ajouter un client'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="nom" value="Nom" />
                <x-text-input wire:model="nom" id="nom" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
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
            <div>
                <x-input-label for="adresse" value="Adresse" />
                <x-text-input wire:model="adresse" id="adresse" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
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
