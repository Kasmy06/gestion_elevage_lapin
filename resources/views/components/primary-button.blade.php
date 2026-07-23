<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-1.5 rounded-lg bg-farm-green px-4 py-2 text-sm font-medium text-white transition hover:bg-farm-green-dark focus:outline-none focus:ring-2 focus:ring-farm-green focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
