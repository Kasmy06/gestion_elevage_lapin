<div>
    <x-page-header title="Utilisateurs" subtitle="Comptes ayant accès à l'application">
        <x-slot name="actions">
            <x-primary-button wire:click="creer">
                <span class="material-icons text-base">add</span> Ajouter un utilisateur
            </x-primary-button>
        </x-slot>
    </x-page-header>

    <x-flash :message="$flashMessage" :type="$flashType" />

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Nom</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Email</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Rôle</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @foreach ($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-farm-green text-xs font-semibold text-white">
                                    {{ \Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper() }}
                                </span>
                                <span class="text-sm font-medium text-farm-text">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $user->email }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="$user->isAdmin() ? 'purple' : 'gray'">{{ $user->isAdmin() ? 'Administrateur' : 'Éleveur' }}</x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <button type="button" wire:click="modifier({{ $user->id }})" class="font-medium text-farm-blue hover:underline">Modifier</button>
                            @if ($user->id !== auth()->id())
                                <button
                                    type="button"
                                    wire:click="supprimer({{ $user->id }})"
                                    wire:confirm="Supprimer l'utilisateur {{ $user->name }} ?"
                                    class="ms-3 font-medium text-farm-red hover:underline"
                                >
                                    Supprimer
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <x-crud-modal :show="$showModal" :title="$editing ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input wire:model="name" id="name" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="role" value="Rôle" />
                <x-select-input wire:model="role" id="role" class="mt-1 block w-full">
                    <option value="eleveur">Éleveur</option>
                    <option value="admin">Administrateur</option>
                </x-select-input>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password" :value="$editing ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe'" />
                <x-text-input wire:model="password" id="password" type="password" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" wire:click="closeModal">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
</div>
