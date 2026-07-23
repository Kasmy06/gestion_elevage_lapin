@props(['label', 'value', 'icon', 'color' => 'green', 'sub' => null])

@php
$map = [
    'green' => ['border' => 'border-l-farm-green', 'bg' => 'bg-farm-green-pale', 'text' => 'text-farm-green'],
    'orange' => ['border' => 'border-l-farm-orange', 'bg' => 'bg-farm-orange-pale', 'text' => 'text-farm-orange'],
    'blue' => ['border' => 'border-l-farm-blue', 'bg' => 'bg-farm-blue-pale', 'text' => 'text-farm-blue'],
    'red' => ['border' => 'border-l-farm-red', 'bg' => 'bg-farm-red-pale', 'text' => 'text-farm-red'],
    'purple' => ['border' => 'border-l-farm-purple', 'bg' => 'bg-farm-purple-pale', 'text' => 'text-farm-purple'],
];
$c = $map[$color] ?? $map['green'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-xl border-l-4 {$c['border']} bg-white p-4 shadow-sm"]) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-farm-text-light">{{ $label }}</p>
            <p class="mt-1.5 text-2xl font-bold leading-none text-farm-text">{{ $value }}</p>
            @if ($sub)
                <p class="mt-1 text-[11px] text-farm-text-light">{{ $sub }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $c['bg'] }} {{ $c['text'] }}">
                <span class="material-icons text-xl">{{ $icon }}</span>
            </div>
        @endif
    </div>
</div>
