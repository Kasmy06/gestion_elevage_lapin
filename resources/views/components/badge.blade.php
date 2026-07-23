@props(['color' => 'gray'])

@php
$map = [
    'gray' => 'bg-gray-100 text-gray-600',
    'green' => 'bg-farm-green-pale text-farm-green-dark',
    'red' => 'bg-farm-red-pale text-red-800',
    'yellow' => 'bg-farm-orange-pale text-orange-800',
    'orange' => 'bg-farm-orange-pale text-orange-800',
    'blue' => 'bg-farm-blue-pale text-blue-900',
    'indigo' => 'bg-farm-purple-pale text-farm-purple',
    'purple' => 'bg-farm-purple-pale text-farm-purple',
];
$colors = $map[$color] ?? $map['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium $colors"]) }}>
    {{ $slot }}
</span>
