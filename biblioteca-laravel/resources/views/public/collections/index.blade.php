@extends('layouts.app')

@section('title', 'Colecciones — Biblioteca')

@section('content')

<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        showCatalog:   {{ ($query || request('todos')) ? 'true' : 'false' }},
        showCarousels: {{ ($query || request('todos')) ? 'false' : 'true' }},
        searching:     {{ $query ? 'true' : 'false' }},

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
    }"
>

    {{-- ══════════════════════════════════════════
         CABECERA
    ══════════════════════════════════════════ --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">Colecciones</h1>
        <p class="text-gray-500 dark:text-gray-400">Sagas y series completas de libros</p>
    </div>

    {{-- ══════════════════════════════════════════
         BARRA DE FILTROS  (siempre visible)
    ══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-3 mb-8 shadow-sm">
        <form
            method="GET"
            class="flex flex-wrap items-center gap-2"
        >
            {{-- Preservar estado "todos" al buscar --}}
            <input type="hidden" name="todos" x-bind:value="showCatalog ? '1' : ''" />

            {{-- Búsqueda --}}
            <div class="relative flex-1 min-w-52">
                <input
                    type="text"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Buscar colección..."
                    class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100
                           focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
            </div>

            {{-- Separador vertical --}}
            <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-gray-700 flex-shrink-0"></div>

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
                       focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option value="name"        {{ $sortBy === 'name'        ? 'selected' : '' }}>Nombre A–Z</option>
                <option value="books_count" {{ $sortBy === 'books_count' ? 'selected' : '' }}>Más libros</option>
                <option value="views"       {{ $sortBy === 'views'       ? 'selected' : '' }}>Más vistas</option>
            </select>

            {{-- Botón filtrar --}}
            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold
                       rounded-xl transition-colors flex-shrink-0"
            >
                Filtrar
            </button>

            {{-- Limpiar --}}
            @if($query)
            <a
                href="{{ request()->url() }}"
                class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors"
            >
                Limpiar
            </a>
            @endif

        </form>
    </div>

    {{-- ══════════════════════════════════════════
         CARRUSELES
    ══════════════════════════════════════════ --}}
    <div
        x-show="showCarousels && !searching"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-220"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        @foreach($featuredLists as $list)
        <x-carousel :title="$list['title']" :items="$list['items']" type="collection" />
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════
         BOTÓN "VER TODAS LAS COLECCIONES"
    ══════════════════════════════════════════ --}}
    <div
        x-show="showCarousels && !searching && !showCatalog"
        x-transition:enter="transition ease-out duration-200 delay-100"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="mt-10 flex justify-center"
    >
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
                   transition-all duration-200"
        >
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Ver todas las colecciones
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-y-0.5"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    {{-- ══════════════════════════════════════════
         CATÁLOGO COMPLETO
    ══════════════════════════════════════════ --}}
    <div
        x-show="showCatalog || searching"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-6"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="mt-8"
    >
        {{-- Separador con label contextual --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
            <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                @if($query) Resultados de búsqueda @else Todas las colecciones @endif
            </span>
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
        </div>

        {{-- Contador --}}
        @if(!empty($collections))
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ count($collections) }} resultado(s)
            @if($query)
                para <strong class="text-gray-700 dark:text-gray-300">"{{ $query }}"</strong>
            @endif
        </p>
        @endif

        {{-- Grid de colecciones --}}
        @if(empty($collections))
        <div class="text-center py-16">
            <p class="text-gray-400 dark:text-gray-500 text-lg mb-1">Sin resultados</p>
            <p class="text-sm text-gray-400 dark:text-gray-600 mb-4">
                No se encontraron colecciones con ese nombre.
            </p>
            @if($query)
            <a href="{{ request()->url() }}"
               class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Limpiar filtros
            </a>
            @endif
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($collections as $collection)
            <a href="{{ route('collections.show', $collection['slug']) }}"
               class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                      overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                <div class="relative h-40 overflow-hidden bg-gray-100 dark:bg-gray-700">
                    <img
                        src="{{ isset($collection['cover_image']) && $collection['cover_image'] ? asset('storage/'.$collection['cover_image']) : asset('images/default-cover.png') }}"
                        alt="{{ $collection['name'] }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                        <h3 class="text-white font-bold text-lg leading-tight">
                            {{ $collection['name'] }}
                        </h3>
                    </div>
                </div>
                <div class="px-4 py-3 flex items-center justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $collection['books_count'] }} libro(s)
                    </p>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400
                                group-hover:translate-x-1 transition-all"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Botón "Ocultar colecciones" --}}
        <div
            x-show="!searching"
            class="mt-12 flex justify-center"
        >
            <button
                type="button"
                @click="closeCatalog()"
                class="group inline-flex items-center gap-2 px-5 py-2.5
                       text-sm text-gray-400 dark:text-gray-500
                       hover:text-gray-600 dark:hover:text-gray-300
                       border border-transparent hover:border-gray-200 dark:hover:border-gray-700
                       rounded-xl transition-all duration-200"
            >
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-y-0.5"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
                Ocultar colecciones
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
                if (el) el.scrollBy({ left: -600, behavior: 'smooth' });
            },
            scrollRight(id) {
                const el = document.getElementById('carousel-' + id);
                if (el) el.scrollBy({ left: 600, behavior: 'smooth' });
            }
        };
    }
</script>
@endpush