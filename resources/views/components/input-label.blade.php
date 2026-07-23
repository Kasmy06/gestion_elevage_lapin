@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-medium text-farm-text-light']) }}>
    {{ $value ?? $slot }}
</label>
