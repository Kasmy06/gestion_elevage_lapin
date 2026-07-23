<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts & icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-farm-text antialiased">
        <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-farm-green-dark via-farm-green to-green-600 px-4 py-10">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-[0_20px_60px_rgba(0,0,0,.3)]">
                <a href="/" wire:navigate class="mb-8 flex items-center gap-3">
                    <img src="{{ \App\Models\Parametre::current()->logoUrl() }}" alt="{{ config('app.name') }}" class="h-12 w-12 shrink-0 rounded-full object-cover">
                    <div>
                        <h1 class="text-lg font-bold leading-tight text-farm-text">{{ config('app.name') }}</h1>
                        <span class="text-xs text-farm-text-light">Gestion d'élevage cunicole</span>
                    </div>
                </a>

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
