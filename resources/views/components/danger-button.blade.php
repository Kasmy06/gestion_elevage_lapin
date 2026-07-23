<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-1.5 rounded-lg bg-farm-red px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-farm-red focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
