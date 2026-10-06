<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php($parametre = \App\Models\Parametre::current())
        <title>{{ $parametre->nom_ferme }}</title>
        <link rel="icon" href="{{ $parametre->logoUrl() }}">

        <!-- Fonts & icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <!-- Charts : servi localement (public/vendor) plutôt que par CDN pour fonctionner hors ligne / réseau limité.
             Chargé de façon bloquante pour être disponible avant l'init d'Alpine/Livewire. -->
        <script src="{{ asset('vendor/chart.umd.min.js') }}"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-farm-bg lg:flex">
            <livewire:layout.navigation />

            <div class="min-w-0 flex-1">
                <livewire:layout.topbar />

                <!-- Page Content -->
                <main>
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        <x-session-flash />

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
