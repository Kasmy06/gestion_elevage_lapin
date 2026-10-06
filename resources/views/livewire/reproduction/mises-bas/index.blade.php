<div>
    <x-page-header title="Mises bas & sevrages"subtitle="Suivi des portées jusqu'au sevrage (chapitre 4.5-4.6 de l'Agrodok)" />

    <x-flash :message="$flashMessage" :type="$flashType" />

    @if ($sailliesEnAttenteMiseBas->isNotEmpty())
        <div class="mb-4 rounded-xl border border-farm-orange/30 bg-farm-orange/10 p-4 text-sm">
            <p class="font-medium text-farm-orange">
                {{ $sailliesEnAttenteMiseBas->count() }} mise{{ $sailliesEnAttenteMiseBas->count() > 1 ? 's' : '' }} bas à enregistrer
            </p>
            <ul class="mt-2 space-y-1 text-farm-text">
                @foreach ($sailliesEnAttenteMiseBas as $saillie)
                    <li>
                        @if ($saillie->femelle)
                            <a href="{{ route('lapins.show', $saillie->femelle) }}" wire:navigate class="font-medium text-farm-green hover:underline">{{ $saillie->femelle->identifiant }}</a>
                        @else
                            <span class="text-farm-text-light">#{{ $saillie->femelle_id }} (supprimé)</span>
                        @endif
                        — mise bas prévue le {{ $saillie->date_mise_bas_prevue->format('d/m/Y') }}
                    </li>
                @endforeach
            </ul>
            <p class="mt-2 text-xs text-farm-text-light">
                Une mise bas s'enregistre depuis la page
                <a href="{{ route('reproduction.saillies.index') }}" wire:navigate class="font-medium underline">Saillies</a>,
                en cliquant sur « Mise bas » sur la ligne de la saillie concernée.
            </p>
        </div>
    @endif

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Femelle</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Mise bas</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Nés vivants / morts-nés</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Sevrage</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($misesBas as $miseBas)
                    <tr wire:key="mise-bas-{{ $miseBas->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3">
                            @if ($miseBas->femelle)
                                <a href="{{ route('lapins.show', $miseBas->femelle) }}" wire:navigate class="font-medium text-farm-green hover:underline">{{ $miseBas->femelle->identifiant }}</a>
                            @else
                                <span class="text-farm-text-light">#{{ $miseBas->femelle_id }} (supprimé)</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $miseBas->date_mise_bas->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">
                            {{ $miseBas->nb_nes_vivants }} / {{ $miseBas->nb_morts_nes }}
                            @if ($miseBas->mortalites->isNotEmpty())
                                <span class="ms-2 text-farm-red">
                                    · décès : {{ $miseBas->nbMortsAuStade(\App\Models\MortaliteLapereaux::STADE_NAISSANCE) }} après naissance, {{ $miseBas->nbMortsAuStade(\App\Models\MortaliteLapereaux::STADE_SEVRAGE) }} après sevrage
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-farm-text-light">
                            @if ($miseBas->sevrage)
                                Sevré le {{ $miseBas->sevrage->date_sevrage->format('d/m/Y') }} ({{ $miseBas->sevrage->nb_sevres }})
                                @if ($miseBas->sevrage->lapereaux_generes)
                                    <x-badge color="green" class="ms-2">Fiches générées</x-badge>
                                @endif
                            @else
                                <span class="font-medium text-farm-orange">Sevrage prévu le {{ $miseBas->date_sevrage_prevue->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <div class="flex flex-col items-end gap-1">
                                <button type="button" wire:click="ouvrirModification({{ $miseBas->id }})" class="font-medium text-farm-blue hover:underline">
                                    Modifier
                                </button>
                                @if (! $miseBas->sevrage || ! $miseBas->sevrage->lapereaux_generes)
                                    <button type="button" wire:click="ouvrirMortalite({{ $miseBas->id }})" class="font-medium text-farm-red hover:underline">
                                        Déclarer un décès
                                    </button>
                                @endif
                                @if (! $miseBas->sevrage)
                                    <button type="button" wire:click="ouvrirSevrage({{ $miseBas->id }})" class="font-medium text-farm-green hover:underline">
                                        Enregistrer le sevrage
                                    </button>
                                @elseif (! $miseBas->sevrage->lapereaux_generes)
                                    <button type="button" wire:click="ouvrirGenerationLapereaux({{ $miseBas->id }})" class="font-medium text-farm-green hover:underline">
                                        Générer les lapereaux
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-farm-text-light">
                            Aucune mise bas enregistrée pour l'instant. Elles apparaissent ici après diagnostic positif d'une saillie.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $misesBas->links() }}
    </div>

    <x-crud-modal :show="$modal === 'mortalite'" title="Déclarer un décès de lapereau">
        @if ($selectedMiseBas)
            <form wire:submit="enregistrerMortalite" class="space-y-4">
                <p class="text-sm text-farm-text-light">
                    Portée de <strong class="text-farm-text">{{ $selectedMiseBas->femelle?->identifiant ?? '#'.$selectedMiseBas->femelle_id }}</strong>.
                    Ce décès concerne un lapereau non encore identifié (sans fiche Lapin).
                </p>
                <div>
                    <x-input-label for="mortaliteStade" value="Stade du décès" />
                    <x-select-input wire:model="mortaliteStade" id="mortaliteStade" class="mt-1 block w-full">
                        @foreach (\App\Models\MortaliteLapereaux::STADES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('mortaliteStade')" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="mortaliteDate" value="Date du décès" />
                        <x-text-input wire:model="mortaliteDate" id="mortaliteDate" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('mortaliteDate')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mortaliteNombre" value="Nombre de lapereaux" />
                        <x-text-input wire:model="mortaliteNombre" id="mortaliteNombre" type="number" min="1" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('mortaliteNombre')" class="mt-2" />
                    </div>
                </div>
                <div>
                    <x-input-label for="mortaliteNotes" value="Cause ou observations (optionnel)" />
                    <x-textarea-input wire:model="mortaliteNotes" id="mortaliteNotes" rows="2" class="mt-1 block w-full"></x-textarea-input>
                    <x-input-error :messages="$errors->get('mortaliteNotes')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                    <x-primary-button>Enregistrer</x-primary-button>
                </div>
            </form>
        @endif
    </x-crud-modal>

    <x-crud-modal :show="$modal === 'edit'" title="Modifier la mise bas">
        @if ($selectedMiseBas)
            <form wire:submit="modifierMiseBas" class="space-y-4">
                <p class="text-sm text-farm-text-light">
                    Portée de <strong class="text-farm-text">{{ $selectedMiseBas->femelle?->identifiant ?? '#'.$selectedMiseBas->femelle_id }}</strong>
                </p>
                <div>
                    <x-input-label for="date_mise_bas" value="Date de mise bas" />
                    <x-text-input wire:model="date_mise_bas" id="date_mise_bas" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_mise_bas')" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nb_nes_vivants" value="Nés vivants" />
                        <x-text-input wire:model="nb_nes_vivants" id="nb_nes_vivants" type="number" min="0" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('nb_nes_vivants')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nb_morts_nes" value="Morts-nés" />
                        <x-text-input wire:model="nb_morts_nes" id="nb_morts_nes" type="number" min="0" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('nb_morts_nes')" class="mt-2" />
                    </div>
                </div>
                <div>
                    <x-input-label for="notes" value="Notes" />
                    <x-textarea-input wire:model="notes" id="notes" rows="3" class="mt-1 block w-full"></x-textarea-input>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                    <x-primary-button>Enregistrer</x-primary-button>
                </div>
            </form>
        @endif
    </x-crud-modal>

    <x-crud-modal :show="$modal === 'sevrage'" title="Enregistrer le sevrage">
        @if ($selectedMiseBas)
            <form wire:submit="enregistrerSevrage" class="space-y-4">
                <p class="text-sm text-farm-text-light">
                    Portée de <strong class="text-farm-text">{{ $selectedMiseBas->femelle?->identifiant ?? '#'.$selectedMiseBas->femelle_id }}</strong>, née le {{ $selectedMiseBas->date_mise_bas->format('d/m/Y') }}
                    ({{ $selectedMiseBas->nb_nes_vivants }} nés vivants).
                </p>
                <div>
                    <x-input-label for="date_sevrage" value="Date du sevrage" />
                    <x-text-input wire:model="date_sevrage" id="date_sevrage" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_sevrage')" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nb_sevres" value="Nombre sevré" />
                        <x-text-input wire:model="nb_sevres" id="nb_sevres" type="number" min="0" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('nb_sevres')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="poids_moyen_g" value="Poids moyen (g)" />
                        <x-text-input wire:model="poids_moyen_g" id="poids_moyen_g" type="number" min="0" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('poids_moyen_g')" class="mt-2" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                    <x-primary-button>Enregistrer</x-primary-button>
                </div>
            </form>
        @endif
    </x-crud-modal>

    <x-crud-modal :show="$modal === 'lapereaux'" title="Générer les fiches des lapereaux">
        @if ($selectedMiseBas)
            <div class="space-y-4">
                <p class="text-sm text-farm-text-light">
                    {{ $selectedSevrageNbSevres }} fiche(s) « Lapin » seront créées pour cette portée, avec la généalogie déjà renseignée.
                    Vous pourrez ensuite les sexer et compléter chaque fiche individuellement.
                </p>
                <div>
                    <x-input-label for="cage_id" value="Clapier d'engraissement (optionnel)" />
                    <x-select-input wire:model="cage_id" id="cage_id" class="mt-1 block w-full">
                        <option value="">—</option>
                        @foreach ($cages as $cage)
                            <option value="{{ $cage->id }}">{{ $cage->numero }} ({{ \App\Models\Cage::TYPES[$cage->type] }})</option>
                        @endforeach
                    </x-select-input>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                    <x-primary-button wire:click="genererLapereaux">Générer</x-primary-button>
                </div>
            </div>
        @endif
    </x-crud-modal>
</div>
