<nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50"
    x-data="{ mobileOpen: false, searchOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-indigo-600 dark:text-indigo-400">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Biblioteca
            </a>

            {{-- Links de navegación (desktop) --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('home') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    Inicio
                </a>
                <a href="{{ route('books.index') }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('books.*') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    Libros
                </a>
                <a href="{{ route('magazines.index') }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('magazines.*') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    Revistas
                </a>
                <a href="{{ route('collections.index') }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('collections.*') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    Colecciones
                </a>
                <a href="{{ route('authors.index') }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('authors.*') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    Autores
                </a>
            </div>

            {{-- Acciones derecha --}}
            <div class="flex items-center gap-2">

                {{-- Búsqueda live --}}
                <button @click="searchOpen = !searchOpen"
                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                </button>

                {{-- Dark mode toggle --}}
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                {{-- Usuario autenticado --}}
                @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">

                        @role('student')
                        <a href="{{ route('student.dashboard') }}"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Mi cuenta
                        </a>
                        @endrole

                        @role('librarian')
                        <a href="{{ route('librarian.dashboard') }}"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Panel Bibliotecario
                        </a>
                        @endrole

                        @role('admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Panel Admin
                        </a>
                        @endrole

                        <hr class="my-1 border-gray-200 dark:border-gray-700" />

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}"
                    class="px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    Registrarse
                </a>
                @endauth

                {{-- Menú móvil --}}
                <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Barra de búsqueda live --}}
        <div x-show="searchOpen" x-transition class="pb-3" x-data="liveSearch()">
            <div class="relative">
                <input type="text"
                    x-model="query"
                    @input.debounce.400ms="search()"
                    placeholder="Buscar libros, revistas, autores..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                </svg>

                {{-- Resultados --}}
                <div x-show="results.length > 0"
                    class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 max-h-80 overflow-y-auto">
                    <template x-for="result in results" :key="result.id">
                        <a :href="'/libros/' + result.isbn"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-8 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="result.title"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="result.author?.name ?? ''"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        {{-- Menú móvil --}}
        <div x-show="mobileOpen" x-transition class="md:hidden pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Inicio</a>
            <a href="{{ route('books.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Libros</a>
            <a href="{{ route('magazines.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Revistas</a>
            <a href="{{ route('collections.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Colecciones</a>
            <a href="{{ route('authors.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Autores</a>
        </div>
    </div>
</nav>