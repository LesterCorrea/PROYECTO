<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: true }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Panel') — Biblioteca</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Contenido --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar del panel --}}
            @include('partials.topbar')

            {{-- Flash --}}
            @include('partials.flash')

            {{-- Página --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>