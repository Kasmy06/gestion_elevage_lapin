@props(['active'])

<div class="mb-5 flex gap-4 border-b border-farm-border">
    <a
        href="{{ route('alimentation.aliments.index') }}"
        wire:navigate
        class="border-b-2 px-1 pb-3 text-sm font-medium {{ $active === 'aliments' ? 'border-farm-green text-farm-green' : 'border-transparent text-farm-text-light hover:text-farm-text' }}"
    >
        Stock d'aliments
    </a>
    <a
        href="{{ route('alimentation.mouvements.index') }}"
        wire:navigate
        class="border-b-2 px-1 pb-3 text-sm font-medium {{ $active === 'mouvements' ? 'border-farm-green text-farm-green' : 'border-transparent text-farm-text-light hover:text-farm-text' }}"
    >
        Journal des mouvements
    </a>
</div>
