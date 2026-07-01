<aside class="w-64 flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col h-full overflow-y-auto"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    x-transition>

    {{-- Logo --}}
    <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-200 dark:border-gray-800">
        <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span class="font-bold text-gray-900 dark:text-gray-100">Biblioteca</span>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 px-3 py-4 space-y-1">

        {{-- ── BIBLIOTECARIO ─────────────────────── --}}
        @role('librarian|admin')

        {{-- Dashboard --}}
        <x-sidebar-link route="librarian.dashboard" icon="home">
            Dashboard
        </x-sidebar-link>

        {{-- Catálogo --}}
        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Catálogo
        </p>
        <x-sidebar-link route="librarian.libros.index" icon="book">
            Libros
        </x-sidebar-link>
        <x-sidebar-link route="librarian.revistas.index" icon="newspaper">
            Revistas
        </x-sidebar-link>
        <x-sidebar-link route="librarian.autores.index" icon="user">
            Autores
        </x-sidebar-link>
        <x-sidebar-link route="librarian.colecciones.index" icon="collection">
            Colecciones
        </x-sidebar-link>
        <x-sidebar-link route="librarian.categorias.index" icon="tag">
            Categorías
        </x-sidebar-link>
        <x-sidebar-link route="librarian.editoriales.index" icon="office">
            Editoriales
        </x-sidebar-link>

        {{-- Gestión --}}
        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Gestión
        </p>
        <x-sidebar-link route="librarian.reservations" icon="calendar">
            Reservas
        </x-sidebar-link>
        <x-sidebar-link route="librarian.loans.index" icon="clipboard">
            Préstamos
        </x-sidebar-link>
        <x-sidebar-link route="librarian.fines.index" icon="cash">
            Multas
        </x-sidebar-link>
        <x-sidebar-link route="librarian.carruseles.index" icon="view-list">
            Carruseles
        </x-sidebar-link>

        @endrole

        {{-- ── ADMIN ─────────────────────────────── --}}
        @role('admin')

        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Administración
        </p>
        <x-sidebar-link route="admin.dashboard" icon="chart">
            Dashboard Admin
        </x-sidebar-link>
        <x-sidebar-link route="admin.usuarios.index" icon="users">
            Usuarios
        </x-sidebar-link>
        <x-sidebar-link route="admin.reports.index" icon="document">
            Reportes
        </x-sidebar-link>
        <x-sidebar-link route="admin.logs.index" icon="shield">
            Logs de actividad
        </x-sidebar-link>

        @endrole
    </nav>

    {{-- Footer sidebar --}}
    <div class="px-3 py-4 border-t border-gray-200 dark:border-gray-800">
        <a href="{{ route('home') }}"
            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Ir al sitio público
        </a>
    </div>
</aside>