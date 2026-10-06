<div>
    <x-page-header title="Comptabilité" subtitle="Recettes, dépenses et profit de l'élevage">
        <x-slot name="actions">
            <div class="flex items-center gap-2">
                <x-select-input wire:model.live="mois" class="text-sm" :disabled="$toutesPeriodes">
                    @foreach (['1'=>'Janvier','2'=>'Février','3'=>'Mars','4'=>'Avril','5'=>'Mai','6'=>'Juin','7'=>'Juillet','8'=>'Août','9'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-select-input>
                <x-select-input wire:model.live="annee" class="text-sm" :disabled="$toutesPeriodes">
                    @foreach (range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-select-input>
                <label class="flex items-center gap-1.5 text-sm text-farm-text-light">
                    <input type="checkbox" wire:model.live="toutesPeriodes" class="rounded border-farm-border text-farm-green focus:ring-farm-green">
                    Toutes les périodes
                </label>
            </div>
            <x-primary-button wire:click="ouvrirDepense">
                <span class="material-icons text-base">add</span> Dépense
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    @php($devise = \App\Models\Parametre::current()->devise)

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Recettes du mois" :value="number_format($recettes, 0, ',', ' ').' '.$devise" icon="trending_up" color="green" />
        <x-stat-card label="Dépenses du mois" :value="number_format($depenses, 0, ',', ' ').' '.$devise" icon="trending_down" color="red" />
        <x-stat-card label="Profit du mois" :value="number_format($profit, 0, ',', ' ').' '.$devise" icon="account_balance_wallet" :color="$profit >= 0 ? 'purple' : 'red'" />
    </div>

    <div class="mb-6 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
        <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-farm-text">
            <span class="material-icons text-base text-farm-green">show_chart</span> Recettes &amp; dépenses (6 derniers mois)
        </h3>
        <div wire:ignore
             x-data="comptaChart(@js($chartLabels), @js($chartRecettes), @js($chartDepenses))"
             x-init="init()"
             x-on:livewire:navigating.window="destroy()"
        >
            <canvas x-ref="canvas" height="90"></canvas>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
        <h3 class="mb-3 text-sm font-semibold text-farm-text">Dernières ventes du mois</h3>
        @forelse ($dernieresVentes as $vente)
            <div class="flex items-center justify-between border-b border-farm-bg py-2 text-sm last:border-0">
                <span class="text-farm-text">{{ $vente->description }} @if($vente->client) <span class="text-farm-text-light">— {{ $vente->client->nom }}</span> @endif</span>
                <span class="font-medium text-farm-green">+{{ number_format($vente->montant_total, 0, ',', ' ') }}</span>
            </div>
        @empty
            <p class="text-sm text-farm-text-light">Aucune vente ce mois-ci.</p>
        @endforelse
        <a href="{{ route('ventes.index') }}" wire:navigate class="mt-3 inline-block text-sm text-farm-green hover:underline">Voir toutes les ventes →</a>
    </div>

    <x-table-card>
        <div class="flex items-center justify-between border-b border-farm-border px-4 py-3">
            <h3 class="text-sm font-semibold text-farm-text">{{ $toutesPeriodes ? 'Toutes les dépenses' : 'Dépenses du mois' }}</h3>
            <button wire:click="exporterDepenses" class="inline-flex items-center gap-1.5 text-sm font-medium text-farm-green hover:underline">
                <span class="material-icons text-base">download</span> Export CSV
            </button>
        </div>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Date</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Libellé</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Catégorie</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Notes</th>
                    <th class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Montant</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($depensesPeriode as $depense)
                    <tr wire:key="depense-{{ $depense->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $depense->date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-farm-text">{{ $depense->libelle }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ \App\Models\Depense::CATEGORIES[$depense->categorie] }}</td>
                        <td class="px-4 py-3 text-sm text-farm-text-light">{{ $depense->notes ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-farm-red">-{{ number_format($depense->montant, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-farm-text-light">Aucune dépense pour cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $depensesPeriode->links() }}
    </div>

    <x-crud-modal :show="$showModal" title="Nouvelle dépense">
        <form wire:submit="enregistrerDepense" class="space-y-4">
            <div>
                <x-input-label for="libelle" value="Libellé" />
                <x-text-input wire:model="libelle" id="libelle" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('libelle')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="categorie" value="Catégorie" />
                    <x-select-input wire:model="categorie" id="categorie" class="mt-1 block w-full">
                        @foreach (\App\Models\Depense::CATEGORIES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="montant" value="Montant" />
                    <x-text-input wire:model="montant" id="montant" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('montant')" class="mt-2" />
                </div>
            </div>
            <div>
                <x-input-label for="date" value="Date" />
                <x-text-input wire:model="date" id="date" type="date" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('date')" class="mt-2" />
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

    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('comptaChart', (labels, recettes, depenses) => ({
                        chart: null,
                        init() {
                            if (Chart.getChart(this.$refs.canvas)) return;
                            this.chart = new Chart(this.$refs.canvas, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        { label: 'Recettes', data: recettes, backgroundColor: '#2E7D32', borderRadius: 4 },
                                        { label: 'Dépenses', data: depenses, backgroundColor: '#D32F2F', borderRadius: 4 },
                                    ],
                                },
                                options: {
                                    responsive: true,
                                    plugins: { legend: { position: 'bottom' } },
                                    scales: { y: { beginAtZero: true } },
                                },
                            });
                        },
                        destroy() {
                            this.chart?.destroy();
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
</div>
