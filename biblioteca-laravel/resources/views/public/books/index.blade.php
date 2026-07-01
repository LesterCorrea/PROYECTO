@extends('layouts.app')

@section('title', 'Libros — Biblioteca')

@section('content')

{{--
    Alpine state:
      showCatalog   → catálogo expandido
      showCarousels → carruseles visibles (se apagan primero antes de mostrar catálogo)
      searching     → búsqueda/filtro activo desde el servidor (PHP)

    Secuencia "Ver todos":
      1. showCarousels = false  → carruseles fade-out (220ms)
      2. setTimeout 240ms       → showCatalog = true → catálogo slide-up (300ms)

    Secuencia "Ocultar":
      1. showCatalog = false    → catálogo fade-out (220ms)
      2. setTimeout 240ms       → showCarousels = true → carruseles fade-in (300ms)
         + scroll suave al top
--}}
<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        showCatalog:   {{ ($query || request('categoria') || request('todos')) ? 'true' : 'false' }},
        showCarousels: {{ ($query || request('categoria') || request('todos')) ? 'false' : 'true' }},
        searching:     {{ ($query || request('categoria'))                     ? 'true' : 'false' }},

        openCatalog() {
            this.showCarousels = false;
            setTimeout(() => { this.showCatalog = true; }, 240);
        },

        closeCatalog() {
            this.showCatalog = false;
            setTimeout(() => {
                this.showCarousels = true;
                this.$nextTick(() => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }, 240);
        },

        ...carouselScroll()
    }">

    {{-- ══════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════ --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">Libros</h1>
        <p class="text-gray-500 dark:text-gray-400">Explora nuestro catálogo completo</p>
    </div>

    {{-- ══════════════════════════════════════════
         BARRA DE FILTROS  (siempre visible)
    ══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-3 mb-8 shadow-sm">
        <form
            method="GET"
            action="{{ route('books.index') }}"
            class="flex flex-wrap items-center gap-2">
            {{-- Preservar estado "todos" al buscar --}}
            <input type="hidden" name="todos" x-bind:value="showCatalog ? '1' : ''" />

            {{-- Búsqueda --}}
            <div class="relative flex-1 min-w-52">
                <input
                    type="text"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Buscar por título, autor, ISBN..."
                    class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100
                           focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow" />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                </svg>
            </div>

            {{-- Separador vertical --}}
            <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-gray-700 flex-shrink-0"></div>

            {{-- Categoría --}}
            <select
                name="categoria"
                class="px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700
                       bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300
                       focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>

            {{-- Ordenar (solo visible con catálogo o búsqueda activa) --}}
            <select
                name="ordenar"
                x-show="showCatalog || searching"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700
                       bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300
                       focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="title" {{ $sortBy === 'title'          ? 'selected' : '' }}>Título A–Z</option>
                <option value="published_year" {{ $sortBy === 'published_year' ? 'selected' : '' }}>Año</option>
                <option value="views" {{ $sortBy === 'views'          ? 'selected' : '' }}>Más vistos</option>
                <option value="loan_count" {{ $sortBy === 'loan_count'     ? 'selected' : '' }}>Más prestados</option>
            </select>

            {{-- Dirección --}}
            <select
                name="direccion"
                x-show="showCatalog || searching"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700
                       bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300
                       focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="asc" {{ $sortDir === 'asc'  ? 'selected' : '' }}>↑ Asc</option>
                <option value="desc" {{ $sortDir === 'desc' ? 'selected' : '' }}>↓ Desc</option>
            </select>

            {{-- Botón filtrar --}}
            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold
                       rounded-xl transition-colors flex-shrink-0">
                Filtrar
            </button>

            {{-- Limpiar --}}
            @if($query || request('categoria'))
            <a
                href="{{ route('books.index') }}"
                class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                Limpiar
            </a>
            @endif

            {{-- Toggle vista --}}
            <div
                class="ml-auto flex gap-1"
                x-show="showCatalog || searching"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <a href="{{ request()->fullUrlWithQuery(['vista' => 'grid']) }}"
                    class="p-2 rounded-lg transition-colors
                          {{ $view === 'grid'
                              ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                              : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    title="Cuadrícula">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['vista' => 'list']) }}"
                    class="p-2 rounded-lg transition-colors
                          {{ $view === 'list'
                              ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                              : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    title="Lista">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </a>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════
         CARRUSELES
         - Ocultos si hay búsqueda activa (searching)
         - Controlados por showCarousels para la animación encadenada
    ══════════════════════════════════════════ --}}
    <div
        x-show="showCarousels && !searching"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-220"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        @foreach($featuredLists as $list)
        <x-carousel :title="$list['title']" :items="$list['items']" type="book" />
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════
         BOTÓN "VER TODOS LOS LIBROS"
         Visible solo cuando: no hay búsqueda Y carruseles visibles Y catálogo cerrado
    ══════════════════════════════════════════ --}}
    <div
        x-show="showCarousels && !searching && !showCatalog"
        x-transition:enter="transition ease-out duration-200 delay-100"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="mt-10 flex justify-center">
        <button
            type="button"
            @click="openCatalog()"
            class="group inline-flex items-center gap-2 px-6 py-3
                   border border-gray-300 dark:border-gray-600
                   text-sm font-semibold text-gray-700 dark:text-gray-300
                   bg-white dark:bg-gray-800
                   rounded-2xl shadow-sm
                   hover:border-indigo-400 dark:hover:border-indigo-500
                   hover:text-indigo-600 dark:hover:text-indigo-400
                   hover:shadow-md
                   transition-all duration-200">
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Ver todos los libros
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-y-0.5"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    {{-- ══════════════════════════════════════════
         CATÁLOGO COMPLETO
         Visible cuando: showCatalog=true O hay búsqueda activa
    ══════════════════════════════════════════ --}}
    <div
        x-show="showCatalog || searching"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-6"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="mt-8">
        {{-- Separador con label contextual --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
            <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                @if($query || request('categoria')) Resultados de búsqueda @else Catálogo completo @endif
            </span>
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
        </div>

        {{-- Contador --}}
        @if(!empty($books))
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ count($books) }} resultado(s)
            @if($query)
            para <strong class="text-gray-700 dark:text-gray-300">"{{ $query }}"</strong>
            @endif
        </p>
        @endif

        {{-- Grid / Lista --}}
        @if(empty($books))
        <div class="text-center py-16">
            <p class="text-gray-400 dark:text-gray-500 text-lg mb-1">Sin resultados</p>
            <p class="text-sm text-gray-400 dark:text-gray-600 mb-4">
                No se encontraron libros con esos filtros.
            </p>
            <a href="{{ route('books.index') }}"
                class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Limpiar filtros
            </a>
        </div>
        @else
        @if($view === 'grid')
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($books as $book)
            <x-book-card :book="$book" view="grid" />
            @endforeach
        </div>
        @else
        <div class="flex flex-col gap-3">
            @foreach($books as $book)
            <x-book-card :book="$book" view="list" />
            @endforeach
        </div>
        @endif
        @endif

        {{-- Botón "Ocultar libros" (solo cuando no hay búsqueda activa) --}}
        <div
            x-show="!searching"
            class="mt-12 flex justify-center">
            <button
                type="button"
                @click="closeCatalog()"
                class="group inline-flex items-center gap-2 px-5 py-2.5
                       text-sm text-gray-400 dark:text-gray-500
                       hover:text-gray-600 dark:hover:text-gray-300
                       border border-transparent hover:border-gray-200 dark:hover:border-gray-700
                       rounded-xl transition-all duration-200">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-y-0.5"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                Ocultar libros
            </button>
        </div>

    </div>{{-- /catálogo --}}

</div>
@endsection

@push('scripts')
<script>
    function carouselScroll() {
        return {
            scrollLeft(id) {
                const el = document.getElementById('carousel-' + id);
                if (el) el.scrollBy({
                    left: -600,
                    behavior: 'smooth'
                });
            },
            scrollRight(id) {
                const el = document.getElementById('carousel-' + id);
                if (el) el.scrollBy({
                    left: 600,
                    behavior: 'smooth'
                });
            }
        };
    }
</script>
@endpush