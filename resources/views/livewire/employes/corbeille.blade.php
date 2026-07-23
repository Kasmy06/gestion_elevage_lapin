<div>
    <x-page-header title="Corbeille" subtitle="Employés supprimés — restaurables tant qu'ils ne sont pas effacés définitivement">
        <x-slot name="actions">
            <a href="{{ route('employes.index') }}" wire:navigate>
                <x-secondary-button>
                    <span class="material-icons text-base">arrow_back</span> Retour aux employés
                </x-secondary-button>
            </a>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Nom</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Poste</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Supprimé le</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($employes as $employe)
                    <tr wire:key="corbeille-employe-{{ $employe->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-farm-text">{{ $employe->nom }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $employe->poste }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $employe->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="restaurer({{ $employe->id }})" class="font-medium text-farm-green hover:underline">
                                Restaurer
                            </button>
                            <button
                                type="button"
                                wire:click="supprimerDefinitivement({{ $employe->id }})"
                                wire:confirm="Supprimer DÉFINITIVEMENT l'employé {{ $employe->nom }} ? Cette action est irréversible."
                                class="ms-3 font-medium text-farm-red hover:underline"
                            >
                                Supprimer définitivement
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-farm-text-light">
                            La corbeille est vide.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $employes->links() }}
    </div>
</div>
