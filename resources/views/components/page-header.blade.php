@props(['title', 'subtitle' => null])

<div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-lg font-semibold leading-tight text-farm-text">
            {{ $title }}
        </h2>
        @if ($subtitle)
            <p class="mt-0.5 text-sm text-farm-text-light">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
