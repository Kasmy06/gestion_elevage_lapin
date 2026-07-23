<div>
    <x-page-header title="Corbeille" subtitle="Lapins supprimés — restaurables tant qu'ils ne sont pas effacés définitivement">
        <x-slot name="actions">
            <a href="{{ route('lapins.index') }}" wire:navigate>
                <x-secondary-button>
                    <span class="material-icons text-base">arrow_back</span> Retour au cheptel
                </x-secondary-button>
            </a>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Identifiant</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Race</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Supprimé le</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Historique lié</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($lapins as $lapin)
                    <tr wire:key="corbeille-{{ $lapin->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-farm-text">{{ $lapin->identifiant }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $lapin->race?->nom ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $lapin->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">
                            @if ($lapin->pesees_count + $lapin->sante_interventions_count + $lapin->sorties_count > 0)
                                {{ $lapin->pesees_count }} pesée(s), {{ $lapin->sante_interventions_count }} suivi(s) santé, {{ $lapin->sorties_count }} sortie(s)
                            @else
                                Aucun
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button
                                type="button"
                                wire:click="restaurer({{ $lapin->id }})"
                                class="font-medium text-farm-green hover:underline"
                            >
                                Restaurer
                            </button>
                            <button
                                type="button"
                                wire:click="supprimerDefinitivement({{ $lapin->id }})"
                                wire:confirm="Supprimer DÉFINITIVEMENT le lapin {{ $lapin->identifiant }} ? Cette action est irréversible et effacera aussi son historique lié (pesées, suivis santé, sorties, saillies)."
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
        {{ $lapins->links() }}
    </div>
</div>
