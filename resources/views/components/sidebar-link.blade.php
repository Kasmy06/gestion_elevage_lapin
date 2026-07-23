@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 rounded-lg border-l-[3px] border-white bg-white/15 px-3 py-2 text-sm font-medium text-white'
            : 'flex items-center gap-3 rounded-lg border-l-[3px] border-transparent px-3 py-2 text-sm font-normal text-white/85 hover:bg-white/10 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <span class="material-icons text-[18px]">{{ $icon }}</span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
