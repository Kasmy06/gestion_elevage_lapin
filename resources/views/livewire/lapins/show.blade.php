<div>
    <x-page-header :title="$lapin->identifiant" subtitle="Fiche du lapin">
        <x-slot name="actions">
            <a href="{{ route('lapins.index') }}" wire:navigate class="text-sm text-farm-text-light hover:text-farm-text">
                Retour à la liste
            </a>
            <a href="{{ route('lapins.edit', $lapin) }}" wire:navigate>
                <x-secondary-button>
                    <span class="material-icons text-base">edit</span> Modifier
                </x-secondary-button>
            </a>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center gap-3">
                    <x-badge :color="$lapin->statutCouleur()" class="text-sm">{{ \App\Models\Lapin::STATUTS[$lapin->statut] }}</x-badge>
                    @if ($lapin->sexe)
                        <span class="text-sm text-farm-text-light">{{ \App\Models\Lapin::SEXES[$lapin->sexe] }}</span>
                    @else
                        <span class="text-sm text-farm-text-light">Non sexé</span>
                    @endif
                </div>

                <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Race</dt>
                        <dd class="mt-1 text-sm text-farm-text">{{ $lapin->race?->nom ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Âge</dt>
                        <dd class="mt-1 text-sm text-farm-text">{{ $lapin->age_lisible ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Poids actuel</dt>
                        <dd class="mt-1 text-sm text-farm-text">{{ $lapin->poids_actuel_g ? number_format($lapin->poids_actuel_g / 1000, 2).' kg' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Clapier</dt>
                        <dd class="mt-1 text-sm text-farm-text">{{ $lapin->cage?->numero ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Origine</dt>
                        <dd class="mt-1 text-sm text-farm-text">{{ \App\Models\Lapin::ORIGINES[$lapin->origine] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Naissance</dt>
                        <dd class="mt-1 text-sm text-farm-text">{{ $lapin->date_naissance?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Père</dt>
                        <dd class="mt-1 text-sm text-farm-text">
                            @if ($lapin->pere)
                                <a href="{{ route('lapins.show', $lapin->pere) }}" wire:navigate class="text-farm-green hover:underline">{{ $lapin->pere->identifiant }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Mère</dt>
                        <dd class="mt-1 text-sm text-farm-text">
                            @if ($lapin->mere)
                                <a href="{{ route('lapins.show', $lapin->mere) }}" wire:navigate class="text-farm-green hover:underline">{{ $lapin->mere->identifiant }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>

                @if ($lapin->notes)
                    <div class="mt-4 border-t border-farm-bg pt-4">
                        <dt class="text-xs uppercase tracking-wide text-farm-text-light">Notes</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-farm-text">{{ $lapin->notes }}</dd>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-farm-text-light">Croissance</h3>
                <div class="mt-4">
                    @if (count($poidsData) >= 2)
                        <div wire:ignore
                             x-data="poidsChart(@js($poidsLabels), @js($poidsData))"
                             x-init="init()"
                             x-on:livewire:navigating.window="destroy()"
                        >
                            <canvas x-ref="canvas" height="90"></canvas>
                        </div>
                    @else
                        <p class="text-sm text-farm-text-light">Pas encore assez de pesées pour un graphique.</p>
                    @endif
                </div>

                <form wire:submit="ajouterPesee" class="mt-4 flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="nouvellePeseeDate" value="Date" />
                        <x-text-input wire:model="nouvellePeseeDate" id="nouvellePeseeDate" type="date" class="mt-1" />
                        <x-input-error :messages="$errors->get('nouvellePeseeDate')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="nouveauPoidsG" value="Poids (g)" />
                        <x-text-input wire:model="nouveauPoidsG" id="nouveauPoidsG" type="number" min="1" class="mt-1" />
                        <x-input-error :messages="$errors->get('nouveauPoidsG')" class="mt-1" />
                    </div>
                    <x-primary-button>Ajouter la pesée</x-primary-button>
                </form>

                @if ($pesees->isNotEmpty())
                    <div class="mt-4 max-h-48 overflow-y-auto">
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-farm-bg">
                                @foreach ($pesees->reverse() as $pesee)
                                    <tr>
                                        <td class="py-1.5 text-farm-text-light">{{ $pesee->date_pesee->format('d/m/Y') }}</td>
                                        <td class="py-1.5 text-right font-medium text-farm-text">{{ number_format($pesee->poids_g / 1000, 2) }} kg</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-farm-text-light">Historique santé</h3>
                    <a href="{{ route('sante.index') }}" wire:navigate class="text-sm text-farm-green hover:underline">Ajouter un suivi</a>
                </div>

                @forelse ($santeInterventions as $intervention)
                    <div class="mt-3 flex items-start justify-between border-b border-farm-bg pb-3 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-farm-text">{{ $intervention->libelle }}</p>
                            <p class="text-xs text-farm-text-light">{{ \App\Models\SanteIntervention::TYPES[$intervention->type] }} — depuis le {{ $intervention->date_debut->format('d/m/Y') }}</p>
                        </div>
                        <x-badge :color="$intervention->statut === 'gueri' ? 'green' : ($intervention->statut === 'deces' ? 'red' : 'orange')">
                            {{ \App\Models\SanteIntervention::STATUTS[$intervention->statut] }}
                        </x-badge>
                    </div>
                @empty
                    <p class="mt-3 text-sm text-farm-text-light">Aucun suivi santé enregistré.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-farm-text-light">Descendance ({{ $descendants->count() }})</h3>
                @forelse ($descendants as $descendant)
                    <a href="{{ route('lapins.show', $descendant) }}" wire:navigate class="mt-3 flex items-center justify-between border-b border-farm-bg pb-2 text-sm last:border-0">
                        <span class="text-farm-green hover:underline">{{ $descendant->identifiant }}</span>
                        <span class="text-farm-text-light">{{ $descendant->date_naissance?->format('d/m/Y') }}</span>
                    </a>
                @empty
                    <p class="mt-3 text-sm text-farm-text-light">Aucun descendant enregistré.</p>
                @endforelse
            </div>

            <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-farm-text-light">Sorties</h3>
                @forelse ($sorties as $sortie)
                    <div class="mt-3 border-b border-farm-bg pb-3 text-sm last:border-0">
                        <p class="font-medium text-farm-text">{{ \App\Models\Sortie::TYPES[$sortie->type] }} — {{ $sortie->date->format('d/m/Y') }}</p>
                        @if ($sortie->prix)
                            <p class="text-farm-text-light">{{ number_format($sortie->prix, 0, ',', ' ') }} {{ \App\Models\Parametre::current()->devise }}</p>
                        @endif
                    </div>
                @empty
                    <p class="mt-3 text-sm text-farm-text-light">Ce lapin est toujours dans l'élevage.</p>
                @endforelse
            </div>
        </div>
    </div>

    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('poidsChart', (labels, data) => ({
                        chart: null,
                        init() {
                            if (Chart.getChart(this.$refs.canvas)) return;
                            this.chart = new Chart(this.$refs.canvas, {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Poids (g)',
                                        data: data,
                                        borderColor: '#2E7D32',
                                        backgroundColor: 'rgba(46,125,50,.1)',
                                        tension: 0.3,
                                        fill: true,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    plugins: { legend: { display: false } },
                                    scales: { y: { beginAtZero: false } },
                                },
                            });
                        },
                        destroy() { this.chart?.destroy(); },
                    }));
                });
            </script>
        @endpush
    @endonce
</div>
