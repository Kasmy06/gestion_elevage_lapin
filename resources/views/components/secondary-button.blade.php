<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-1.5 rounded-lg border border-farm-border bg-white px-4 py-2 text-sm font-medium text-farm-text transition hover:bg-farm-bg focus:outline-none focus:ring-2 focus:ring-farm-green focus:ring-offset-2 disabled:opacity-40']) }}>
    {{ $slot }}
</button>
