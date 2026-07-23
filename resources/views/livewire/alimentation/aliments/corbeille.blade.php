<div>
    <x-page-header title="Corbeille" subtitle="Aliments supprimés — restaurables tant qu'ils ne sont pas effacés définitivement">
        <x-slot name="actions">
            <a href="{{ route('alimentation.aliments.index') }}" wire:navigate>
                <x-secondary-button>
                    <span class="material-icons text-base">arrow_back</span> Retour aux aliments
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
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Type</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Supprimé le</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Mouvements liés</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($aliments as $aliment)
                    <tr wire:key="corbeille-aliment-{{ $aliment->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-farm-text">{{ $aliment->nom }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ \App\Models\Aliment::TYPES[$aliment->type] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $aliment->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $aliment->mouvements_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="restaurer({{ $aliment->id }})" class="font-medium text-farm-green hover:underline">
                                Restaurer
                            </button>
                            <button
                                type="button"
                                wire:click="supprimerDefinitivement({{ $aliment->id }})"
                                wire:confirm="Supprimer DÉFINITIVEMENT l'aliment {{ $aliment->nom }} ? Cette action est irréversible et effacera aussi ses {{ $aliment->mouvements_count }} mouvement(s) de stock lié(s)."
                                class="ms-3 font-medium text-farm-red hover:underline"
                            >
                                Supprimer définitivement
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-farm-text-light">
                            La corbeille est vide.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $aliments->links() }}
    </div>
</div>
