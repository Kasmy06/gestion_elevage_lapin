<div>
    <x-page-header title="Corbeille" subtitle="Dépenses supprimées — restaurables tant qu'elles ne sont pas effacées définitivement">
        <x-slot name="actions">
            <a href="{{ route('comptabilite.index') }}" wire:navigate>
                <x-secondary-button>
                    <span class="material-icons text-base">arrow_back</span> Retour à la comptabilité
                </x-secondary-button>
            </a>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Date</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Libellé</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Catégorie</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Montant</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Supprimée le</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($depenses as $depense)
                    <tr wire:key="corbeille-depense-{{ $depense->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $depense->date->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $depense->libelle }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ \App\Models\Depense::CATEGORIES[$depense->categorie] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-farm-text">{{ number_format($depense->montant, 0, ',', ' ') }} {{ \App\Models\Parametre::current()->devise }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $depense->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="restaurer({{ $depense->id }})" class="font-medium text-farm-green hover:underline">
                                Restaurer
                            </button>
                            <button
                                type="button"
                                wire:click="supprimerDefinitivement({{ $depense->id }})"
                                wire:confirm="Supprimer DÉFINITIVEMENT la dépense « {{ $depense->libelle }} » ? Cette action est irréversible."
                                class="ms-3 font-medium text-farm-red hover:underline"
                            >
                                Supprimer définitivement
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-farm-text-light">
                            La corbeille est vide.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $depenses->links() }}
    </div>
</div>
