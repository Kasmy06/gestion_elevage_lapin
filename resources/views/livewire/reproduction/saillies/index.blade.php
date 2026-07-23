<div>
    <x-page-header title="Saillies" subtitle="Accouplement, diagnostic de gestation et mise bas (chapitre 4 de l'Agrodok)">
        <x-slot name="actions">
            <x-primary-button wire:click="ouvrirCreation">
                <span class="material-icons text-base">add</span> Nouvelle saillie
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Femelle</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Mâle</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Date saillie</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Diagnostic</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Suivi</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($saillies as $saillie)
                    <tr wire:key="saillie-{{ $saillie->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3">
                            @if ($saillie->femelle)
                                <a href="{{ route('lapins.show', $saillie->femelle) }}" wire:navigate class="font-medium text-farm-green hover:underline">{{ $saillie->femelle->identifiant }}</a>
                            @else
                                <span class="text-farm-text-light">#{{ $saillie->femelle_id }} (supprimé)</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $saillie->male?->identifiant ?? '#'.$saillie->male_id.' (supprimé)' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $saillie->date_saillie->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="match($saillie->diagnostic_gestation) { 'positif' => 'green', 'negatif' => 'red', default => 'orange' }">
                                {{ \App\Models\Saillie::DIAGNOSTICS[$saillie->diagnostic_gestation] }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3 text-sm text-farm-text-light">
                            @if ($saillie->miseBas)
                                Mise bas le {{ $saillie->miseBas->date_mise_bas->format('d/m/Y') }} ({{ $saillie->miseBas->nb_nes_vivants }} vivants)
                            @elseif ($saillie->diagnosticEnAttente())
                                <span class="font-medium text-farm-orange">Diagnostic à faire (depuis le {{ $saillie->diagnosticPossibleLe()->format('d/m/Y') }})</span>
                            @elseif ($saillie->diagnostic_gestation === 'positif')
                                Mise bas prévue le {{ $saillie->date_mise_bas_prevue->format('d/m/Y') }}
                            @elseif ($saillie->diagnostic_gestation === 'en_attente')
                                Diagnostic possible à partir du {{ $saillie->diagnosticPossibleLe()->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            @if ($saillie->diagnostic_gestation === 'en_attente')
                                <button type="button" wire:click="ouvrirDiagnostic({{ $saillie->id }})" class="font-medium text-farm-green hover:underline">
                                    Diagnostic
                                </button>
                            @elseif ($saillie->enAttenteMiseBas())
                                <button type="button" wire:click="ouvrirMiseBas({{ $saillie->id }})" class="font-medium text-farm-green hover:underline">
                                    Mise bas
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-farm-text-light">
                            Aucune saillie enregistrée pour l'instant.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $saillies->links() }}
    </div>

    <x-crud-modal :show="$modal === 'create'" title="Nouvelle saillie">
        <form wire:submit="enregistrerSaillie" class="space-y-4">
            <div>
                <x-input-label for="femelle_id" value="Femelle" />
                <x-select-input wire:model="femelle_id" id="femelle_id" class="mt-1 block w-full">
                    <option value="">Sélectionner...</option>
                    @foreach ($femelles as $femelle)
                        <option value="{{ $femelle->id }}">{{ $femelle->identifiant }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('femelle_id')" class="mt-2" />
                @if ($femelles->isEmpty())
                    <p class="mt-2 text-xs text-farm-orange">
                        Aucune femelle disponible. Seuls les lapins au statut « Reproducteur » peuvent être saillis —
                        <a href="{{ route('lapins.index', ['sexe' => 'femelle']) }}" wire:navigate class="font-medium underline">voir les femelles</a>
                        et modifier leur statut.
                    </p>
                @endif
            </div>
            <div>
                <x-input-label for="male_id" value="Mâle" />
                <x-select-input wire:model="male_id" id="male_id" class="mt-1 block w-full">
                    <option value="">Sélectionner...</option>
                    @foreach ($males as $male)
                        <option value="{{ $male->id }}">{{ $male->identifiant }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('male_id')" class="mt-2" />
                @if ($males->isEmpty())
                    <p class="mt-2 text-xs text-farm-orange">
                        Aucun mâle disponible. Seuls les lapins au statut « Reproducteur » peuvent être saillis —
                        <a href="{{ route('lapins.index', ['sexe' => 'male']) }}" wire:navigate class="font-medium underline">voir les mâles</a>
                        et modifier leur statut.
                    </p>
                @endif
            </div>
            <div>
                <x-input-label for="date_saillie" value="Date de la saillie" />
                <x-text-input wire:model="date_saillie" id="date_saillie" type="date" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('date_saillie')" class="mt-2" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal :show="$modal === 'diagnostic'" title="Diagnostic de gestation">
        @if ($selectedSaillie)
            <form wire:submit="enregistrerDiagnostic" class="space-y-4">
                <p class="text-sm text-farm-text-light">
                    Femelle <strong class="text-farm-text">{{ $selectedSaillie->femelle?->identifiant ?? '#'.$selectedSaillie->femelle_id }}</strong>, saillie du {{ $selectedSaillie->date_saillie->format('d/m/Y') }}.
                </p>
                <div>
                    <x-input-label for="diagnostic_gestation" value="Résultat" />
                    <x-select-input wire:model="diagnostic_gestation" id="diagnostic_gestation" class="mt-1 block w-full">
                        <option value="positif">Positif (gestante)</option>
                        <option value="negatif">Négatif</option>
                    </x-select-input>
                    <x-input-error :messages="$errors->get('diagnostic_gestation')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date_diagnostic" value="Date du diagnostic" />
                    <x-text-input wire:model="date_diagnostic" id="date_diagnostic" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_diagnostic')" class="mt-2" />
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                    <x-primary-button>Enregistrer</x-primary-button>
                </div>
            </form>
        @endif
    </x-crud-modal>

    <x-crud-modal :show="$modal === 'mise-bas'" title="Enregistrer la mise bas">
        @if ($selectedSaillie)
            <form wire:submit="enregistrerMiseBas" class="space-y-4">
                <p class="text-sm text-farm-text-light">
                    Femelle <strong class="text-farm-text">{{ $selectedSaillie->femelle?->identifiant ?? '#'.$selectedSaillie->femelle_id }}</strong>, mise bas prévue le {{ $selectedSaillie->date_mise_bas_prevue->format('d/m/Y') }}.
                </p>
                <div>
                    <x-input-label for="date_mise_bas" value="Date de la mise bas" />
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
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                    <x-primary-button>Enregistrer</x-primary-button>
                </div>
            </form>
        @endif
    </x-crud-modal>
</div>
