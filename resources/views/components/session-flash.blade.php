@if (session('success') || session('error'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 6000)"
        class="mb-4 flex items-center justify-between rounded-lg border px-4 py-3 text-sm {{ session('error') ? 'border-red-200 bg-farm-red-pale text-red-800' : 'border-green-200 bg-farm-green-pale text-farm-green-dark' }}"
    >
        <span>{{ session('success') ?? session('error') }}</span>
        <button type="button" @click="show = false" class="ms-4 shrink-0 opacity-70 hover:opacity-100">
            <span class="material-icons text-base">close</span>
        </button>
    </div>
@endif
