@props(['message' => null, 'type' => 'success'])

@php
$styles = $type === 'error'
    ? 'bg-farm-red-pale text-red-800 border-red-200'
    : 'bg-farm-green-pale text-farm-green-dark border-green-200';
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => "mb-4 flex items-center justify-between rounded-lg border px-4 py-3 text-sm $styles"]) }}>
        <span>{{ $message }}</span>

        <button type="button" wire:click="clearFlash" class="ms-4 shrink-0 opacity-70 hover:opacity-100">
            <span class="material-icons text-base">close</span>
        </button>
    </div>
@endif
