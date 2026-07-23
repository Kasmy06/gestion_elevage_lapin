@props(['show' => false, 'title' => null, 'maxWidth' => 'lg'])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-on:keydown.escape.window="$wire.closeModal()">
        <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full {{ $maxWidthClass }} rounded-2xl bg-white shadow-[0_20px_60px_rgba(0,0,0,.3)]">
                @if ($title)
                    <div class="flex items-center justify-between px-6 pb-2 pt-6">
                        <h3 class="text-base font-semibold text-farm-text">{{ $title }}</h3>
                        <button type="button" wire:click="closeModal" class="flex h-8 w-8 items-center justify-center rounded-lg bg-farm-bg text-farm-text-light hover:text-farm-text">
                            <span class="material-icons text-lg">close</span>
                        </button>
                    </div>
                @endif

                <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
@endif
