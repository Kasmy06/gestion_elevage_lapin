@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-farm-border text-sm text-farm-text focus:border-farm-green focus:ring-farm-green']) }}>
