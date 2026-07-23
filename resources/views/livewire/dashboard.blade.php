<div>
    <x-page-header title="Tableau de bord" subtitle="Vue d'ensemble de l'élevage" />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Lapins actifs" :value="$totalActifs" icon="pets" color="green" />
        <x-stat-card label="Reproducteurs" :value="$totalReproducteurs" icon="favorite" color="blue" />
        <x-stat-card label="Femelles gestantes" :value="$gestantes" icon="pregnant_woman" color="orange" />
        <x-stat-card label="Naissances ce mois" :value="$naissancesDuMois" icon="child_care" color="purple" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm lg:col-span-2">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-farm-text">
                <span class="material-icons text-base text-farm-green">show_chart</span> Naissances (6 derniers mois)
            </h3>
            <div wire:ignore
                 x-data="dashboardLineChart(@js($chartLabels), @js($chartNaissances))"
                 x-init="init()"
                 x-on:livewire:navigating.window="destroy()"
            >
                <canvas x-ref="canvas" height="90"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-farm-text">
                <span class="material-icons text-base text-farm-green">donut_large</span> Répartition du cheptel
            </h3>
            <div wire:ignore
                 x-data="dashboardDonutChart(@js($repartitionLabels), @js($repartitionData))"
                 x-init="init()"
                 x-on:livewire:navigating.window="destroy()"
            >
                <canvas x-ref="canvas" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-2">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-farm-text-light">Alertes du jour</h3>
        @if ($alertesCount > 0)
            <x-badge color="red">{{ $alertesCount }}</x-badge>
        @else
            <x-badge color="green">Aucune</x-badge>
        @endif
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-farm-text">Diagnostics de gestation à faire</h4>
            @forelse ($diagnosticsAFaire as $saillie)
                <div class="mt-3 flex items-center justify-between border-b border-farm-bg pb-2 text-sm last:border-0">
                    <span>{{ $saillie->femelle?->identifiant ?? '#'.$saillie->femelle_id }} × {{ $saillie->male?->identifiant ?? '#'.$saillie->male_id }}</span>
                    <span class="text-farm-text-light">saillie du {{ $saillie->date_saillie->format('d/m/Y') }}</span>
                </div>
            @empty
                <p class="mt-3 text-sm text-farm-text-light">Rien à signaler.</p>
            @endforelse
            @if ($diagnosticsAFaire->isNotEmpty())
                <a href="{{ route('reproduction.saillies.index') }}" wire:navigate class="mt-3 inline-block text-sm text-farm-green hover:underline">Voir les saillies →</a>
            @endif
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-farm-text">Mises bas imminentes ou en retard</h4>
            @forelse ($misesBasImminentes as $saillie)
                <div class="mt-3 flex items-center justify-between border-b border-farm-bg pb-2 text-sm last:border-0">
                    <span>{{ $saillie->femelle?->identifiant ?? '#'.$saillie->femelle_id }}</span>
                    <span class="{{ $saillie->date_mise_bas_prevue->isPast() ? 'font-medium text-farm-red' : 'text-farm-text-light' }}">
                        prévue le {{ $saillie->date_mise_bas_prevue->format('d/m/Y') }}
                    </span>
                </div>
            @empty
                <p class="mt-3 text-sm text-farm-text-light">Rien à signaler.</p>
            @endforelse
            @if ($misesBasImminentes->isNotEmpty())
                <a href="{{ route('reproduction.saillies.index') }}" wire:navigate class="mt-3 inline-block text-sm text-farm-green hover:underline">Voir les saillies →</a>
            @endif
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-farm-text">Sevrages imminents ou en retard</h4>
            @forelse ($sevragesImminents as $miseBas)
                <div class="mt-3 flex items-center justify-between border-b border-farm-bg pb-2 text-sm last:border-0">
                    <span>{{ $miseBas->femelle?->identifiant ?? '#'.$miseBas->femelle_id }}</span>
                    <span class="{{ $miseBas->date_sevrage_prevue->isPast() ? 'font-medium text-farm-red' : 'text-farm-text-light' }}">
                        prévu le {{ $miseBas->date_sevrage_prevue->format('d/m/Y') }}
                    </span>
                </div>
            @empty
                <p class="mt-3 text-sm text-farm-text-light">Rien à signaler.</p>
            @endforelse
            @if ($sevragesImminents->isNotEmpty())
                <a href="{{ route('reproduction.mises-bas.index') }}" wire:navigate class="mt-3 inline-block text-sm text-farm-green hover:underline">Voir les mises bas →</a>
            @endif
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-farm-text">Stocks d'aliments bas</h4>
            @forelse ($stocksBas as $aliment)
                <div class="mt-3 flex items-center justify-between border-b border-farm-bg pb-2 text-sm last:border-0">
                    <span>{{ $aliment->nom }}</span>
                    <span class="font-medium text-farm-red">{{ $aliment->stock_actuel }} / {{ $aliment->seuil_alerte }} {{ $aliment->unite }}</span>
                </div>
            @empty
                <p class="mt-3 text-sm text-farm-text-light">Rien à signaler.</p>
            @endforelse
            @if ($stocksBas->isNotEmpty())
                <a href="{{ route('alimentation.aliments.index') }}" wire:navigate class="mt-3 inline-block text-sm text-farm-green hover:underline">Voir les stocks →</a>
            @endif
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-farm-text">Suivis santé en cours</h4>
            @forelse ($suivisSante as $intervention)
                <div class="mt-3 flex items-center justify-between border-b border-farm-bg pb-2 text-sm last:border-0">
                    <span>{{ $intervention->lapin?->identifiant ?? '#'.$intervention->lapin_id }} — {{ $intervention->libelle }}</span>
                    <span class="text-farm-text-light">depuis le {{ $intervention->date_debut->format('d/m/Y') }}</span>
                </div>
            @empty
                <p class="mt-3 text-sm text-farm-text-light">Aucun suivi en cours.</p>
            @endforelse
            @if ($suivisSante->isNotEmpty())
                <a href="{{ route('sante.index') }}" wire:navigate class="mt-3 inline-block text-sm text-farm-green hover:underline">Voir la santé →</a>
            @endif
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-farm-text">Femelles disponibles pour saillie ({{ $femellesDisponibles->count() }})</h4>
            @forelse ($femellesDisponibles as $femelle)
                <div class="mt-3 flex items-center justify-between border-b border-farm-bg pb-2 text-sm last:border-0">
                    <a href="{{ route('lapins.show', $femelle) }}" wire:navigate class="text-farm-green hover:underline">{{ $femelle->identifiant }}</a>
                </div>
            @empty
                <p class="mt-3 text-sm text-farm-text-light">Aucune femelle disponible actuellement.</p>
            @endforelse
        </div>
    </div>

    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('dashboardLineChart', (labels, data) => ({
                        chart: null,
                        init() {
                            if (Chart.getChart(this.$refs.canvas)) return;
                            this.chart = new Chart(this.$refs.canvas, {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Naissances',
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
                                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                                },
                            });
                        },
                        destroy() { this.chart?.destroy(); },
                    }));

                    Alpine.data('dashboardDonutChart', (labels, data) => ({
                        chart: null,
                        init() {
                            if (Chart.getChart(this.$refs.canvas)) return;
                            this.chart = new Chart(this.$refs.canvas, {
                                type: 'doughnut',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        data: data,
                                        backgroundColor: ['#1976D2', '#2E7D32', '#FB8C00', '#7B1FA2', '#D32F2F', '#9E9E9E', '#66BB6A'],
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
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
