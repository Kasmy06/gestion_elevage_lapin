<div>
    <x-page-header :title="$lapin ? 'Modifier '.$lapin->identifiant : 'Ajouter un lapin'" subtitle="Fiche d'identification (chapitre 9 de l'Agrodok)" />

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-farm-text-light">Identification</h3>
            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="identifiant" value="Identifiant (n° de boucle)" />
                    <x-text-input wire:model="identifiant" id="identifiant" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('identifiant')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="race_id" value="Race" />
                    <x-select-input wire:model="race_id" id="race_id" class="mt-1 block w-full">
                        <option value="">—</option>
                        @foreach ($races as $race)
                            <option value="{{ $race->id }}">{{ $race->nom }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('race_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="sexe" value="Sexe" />
                    <x-select-input wire:model="sexe" id="sexe" class="mt-1 block w-full">
                        <option value="">Non sexé</option>
                        @foreach (\App\Models\Lapin::SEXES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('sexe')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="date_naissance" value="Date de naissance" />
                    <x-text-input wire:model="date_naissance" id="date_naissance" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="origine" value="Origine" />
                    <x-select-input wire:model="origine" id="origine" class="mt-1 block w-full">
                        @foreach (\App\Models\Lapin::ORIGINES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('origine')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="date_acquisition" value="Date d'acquisition" />
                    <x-text-input wire:model="date_acquisition" id="date_acquisition" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_acquisition')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-farm-border bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-farm-text-light">Généalogie et logement</h3>
            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <x-input-label for="pere_id" value="Père" />
                    <x-select-input wire:model="pere_id" id="pere_id" class="mt-1 block w-full">
                        <option value="">—</option>
                        @foreach ($peres as $pere)
                            <option value="{{ $pere->id }}">{{ $pere->identifiant }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('pere_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="mere_id" value="Mère" />
                    <x-select-input wire:model="mere_id" id="mere_id" class="mt-1 block w-full">
                        <option value="">—</option>
                        @foreach ($meres as $mere)
                            <option value="{{ $mere->id }}">{{ $mere->identifiant }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('mere_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="cage_id" value="Clapier" />
                    <x-select-input wire:model="cage_id" id="cage_id" class="mt-1 block w-full">
                        <option value="">—</option>
                        @foreach ($cages as $cage)
                            <option value="{{ $cage->id }}">{{ $cage->numero }} ({{ \App\Models\Cage::TYPES[$cage->type] }}, {{ $cage->occupation }}/{{ $cage->capacite }})</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('cage_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="statut" value="Statut" />
                    <x-select-input wire:model="statut" id="statut" class="mt-1 block w-full">
                        @foreach (\App\Models\Lapin::STATUTS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="poids_actuel_g" value="Poids actuel (g)" />
                    <x-text-input wire:model="poids_actuel_g" id="poids_actuel_g" type="number" min="0" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('poids_actuel_g')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="photo" value="Photo" />
                    <input wire:model="photo" id="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-farm-text-light file:me-4 file:rounded-md file:border-0 file:bg-farm-green-pale file:px-3 file:py-2 file:text-sm file:font-medium file:text-farm-green hover:file:bg-green-100" />
                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="mt-2 h-20 w-20 rounded-lg object-cover" alt="Aperçu">
                    @elseif ($lapin?->photo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($lapin->photo_path) }}" class="mt-2 h-20 w-20 rounded-lg object-cover" alt="Photo actuelle">
                    @endif
                </div>
            </div>

            <div class="mt-6">
                <x-input-label for="notes" value="Notes" />
                <x-textarea-input wire:model="notes" id="notes" rows="3" class="mt-1 block w-full"></x-textarea-input>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ $lapin ? route('lapins.show', $lapin) : route('lapins.index') }}" wire:navigate class="text-sm text-farm-text-light hover:text-farm-text">
                Annuler
            </a>
            <x-primary-button>Enregistrer</x-primary-button>
        </div>
    </form>
</div>
