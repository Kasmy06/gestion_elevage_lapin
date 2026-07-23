<div>
    <x-page-header title="Journal d'activité" subtitle="Traçabilité des actions des utilisateurs sur la plateforme" />

    <div class="mb-4 flex flex-wrap gap-3 rounded-xl bg-farm-bg p-3">
        <x-select-input wire:model.live="userId" id="userId" class="text-sm">
            <option value="">Tous les utilisateurs</option>
            @foreach ($utilisateurs as $utilisateur)
                <option value="{{ $utilisateur->id }}">{{ $utilisateur->name }}</option>
            @endforeach
        </x-select-input>
        <x-select-input wire:model.live="module" id="module" class="text-sm">
            <option value="">Tous les modules</option>
            @foreach (\App\Models\ActivityLog::MODULES as $class => $label)
                <option value="{{ $class }}">{{ $label }}</option>
            @endforeach
        </x-select-input>
        <x-select-input wire:model.live="action" id="action" class="text-sm">
            <option value="">Toutes les actions</option>
            @foreach (\App\Models\ActivityLog::ACTIONS as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-select-input>
    </div>

    <x-table-card>
        <table class="min-w-full divide-y divide-farm-border">
            <thead class="bg-farm-bg">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Date</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Utilisateur</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Action</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Module</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Élément</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-farm-text-light">Détail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-farm-border">
                @forelse ($activites as $activite)
                    <tr wire:key="activite-{{ $activite->id }}" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $activite->created_at->format('d/m/Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text">{{ $activite->user_name ?? $activite->user?->name ?? 'Système' }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <x-badge :color="match($activite->action) {
                                'created' => 'green',
                                'updated' => 'blue',
                                'restored' => 'purple',
                                'force_deleted' => 'red',
                                default => 'orange',
                            }">
                                {{ \App\Models\ActivityLog::ACTIONS[$activite->action] ?? $activite->action }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-farm-text-light">{{ $activite->moduleLabel() }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-farm-text">{{ $activite->subject_label ?? '#'.$activite->subject_id }}</td>
                        <td class="px-4 py-3 text-xs text-farm-text-light">
                            @if ($activite->changes)
                                <ul class="space-y-0.5">
                                    @foreach ($activite->changes as $champ => $valeurs)
                                        <li><span class="font-medium text-farm-text">{{ $champ }}</span> : {{ $valeurs['avant'] ?? '—' }} → {{ $valeurs['apres'] ?? '—' }}</li>
                                    @endforeach
                                </ul>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-farm-text-light">
                            Aucune activité enregistrée pour l'instant.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="mt-4">
        {{ $activites->links() }}
    </div>
</div>
