<div>
    <x-page-header title="Rapports" subtitle="Exports et synthèses de l'élevage" />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-3 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-farm-green-pale text-farm-green">
                <span class="material-icons text-2xl">pets</span>
            </div>
            <h3 class="text-sm font-semibold text-farm-text">Cheptel</h3>
            <p class="flex-1 text-xs text-farm-text-light">{{ $totalLapins }} lapin(s) enregistré(s), toutes générations confondues.</p>
            <button wire:click="exporterCheptel" class="inline-flex items-center gap-1.5 self-start text-sm font-medium text-farm-green hover:underline">
                <span class="material-icons text-base">download</span> Export CSV
            </button>
        </div>

        <div class="flex flex-col gap-3 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-farm-orange-pale text-farm-orange">
                <span class="material-icons text-2xl">favorite</span>
            </div>
            <h3 class="text-sm font-semibold text-farm-text">Reproduction</h3>
            <p class="flex-1 text-xs text-farm-text-light">{{ $totalSaillies }} saillie(s) enregistrée(s) depuis le début de l'élevage.</p>
            <button wire:click="exporterReproduction" class="inline-flex items-center gap-1.5 self-start text-sm font-medium text-farm-green hover:underline">
                <span class="material-icons text-base">download</span> Export CSV
            </button>
        </div>

        <div class="flex flex-col gap-3 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-farm-red-pale text-farm-red">
                <span class="material-icons text-2xl">local_hospital</span>
            </div>
            <h3 class="text-sm font-semibold text-farm-text">Santé</h3>
            <p class="flex-1 text-xs text-farm-text-light">{{ $suiviSanteEnCours }} suivi(s) santé en cours actuellement.</p>
            <button wire:click="exporterSante" class="inline-flex items-center gap-1.5 self-start text-sm font-medium text-farm-green hover:underline">
                <span class="material-icons text-base">download</span> Export CSV
            </button>
        </div>

        <div class="flex flex-col gap-3 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-farm-blue-pale text-farm-blue">
                <span class="material-icons text-2xl">trending_up</span>
            </div>
            <h3 class="text-sm font-semibold text-farm-text">Croissance</h3>
            <p class="flex-1 text-xs text-farm-text-light">{{ $peseesCeMois }} pesée(s) enregistrée(s) ce mois-ci.</p>
            <button wire:click="exporterCroissance" class="inline-flex items-center gap-1.5 self-start text-sm font-medium text-farm-green hover:underline">
                <span class="material-icons text-base">download</span> Export CSV
            </button>
        </div>

        <div class="flex flex-col gap-3 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-farm-purple-pale text-farm-purple">
                <span class="material-icons text-2xl">account_balance</span>
            </div>
            <h3 class="text-sm font-semibold text-farm-text">Financier</h3>
            <p class="flex-1 text-xs text-farm-text-light">
                {{ number_format($ventesCeMois, 0, ',', ' ') }} de recettes, {{ number_format($depensesCeMois, 0, ',', ' ') }} de dépenses ce mois-ci.
            </p>
            <button wire:click="exporterFinancier" class="inline-flex items-center gap-1.5 self-start text-sm font-medium text-farm-green hover:underline">
                <span class="material-icons text-base">download</span> Export CSV
            </button>
        </div>

        <div class="flex flex-col gap-3 rounded-xl border border-farm-border bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-farm-orange-pale text-farm-orange">
                <span class="material-icons text-2xl">restaurant</span>
            </div>
            <h3 class="text-sm font-semibold text-farm-text">Distribution par clapier</h3>
            <p class="flex-1 text-xs text-farm-text-light">Quantités et coûts d'aliments distribués, totalisés par clapier.</p>
            <button wire:click="exporterDistributionParClapier" class="inline-flex items-center gap-1.5 self-start text-sm font-medium text-farm-green hover:underline">
                <span class="material-icons text-base">download</span> Export CSV
            </button>
        </div>
    </div>
</div>
