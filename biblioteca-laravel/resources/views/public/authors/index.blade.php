@extends('layouts.app')

@section('title', 'Autores — Biblioteca')

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
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">Autores</h1>
        <p class="text-gray-500 dark:text-gray-400">Conoce a los autores de nuestra colección</p>
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
                    placeholder="Buscar por nombre o nacionalidad..."
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
                <option value="views"       {{ $sortBy === 'views'       ? 'selected' : '' }}>Más vistos</option>
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
        <x-carousel :title="$list['title']" :items="$list['items']" type="author" />
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════
         BOTÓN "VER TODOS LOS AUTORES"
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
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Ver todos los autores
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
                @if($query) Resultados de búsqueda @else Todos los autores @endif
            </span>
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
        </div>

        {{-- Contador --}}
        @if(!empty($authors))
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ count($authors) }} resultado(s)
            @if($query)
                para <strong class="text-gray-700 dark:text-gray-300">"{{ $query }}"</strong>
            @endif
        </p>
        @endif

        {{-- Grid de autores --}}
        @if(empty($authors))
        <div class="text-center py-16">
            <p class="text-gray-400 dark:text-gray-500 text-lg mb-1">Sin resultados</p>
            <p class="text-sm text-gray-400 dark:text-gray-600 mb-4">
                No se encontraron autores con ese nombre o nacionalidad.
            </p>
            @if($query)
            <a href="{{ request()->url() }}"
               class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Limpiar filtros
            </a>
            @endif
        </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
            @foreach($authors as $author)
            <a href="{{ route('authors.show', $author['id']) }}"
               class="group text-center">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 mb-3
                            shadow-sm group-hover:shadow-md group-hover:ring-2 group-hover:ring-indigo-500
                            transition-all duration-300">
                    <img
                        src="{{ isset($author['image']) && $author['image'] ? asset('storage/'.$author['image']) : asset('images/default-author.png') }}"
                        alt="{{ $author['name'] }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy"
                    />
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 line-clamp-2
                           group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $author['name'] }}
                </h3>
                @if(isset($author['nationality']) && $author['nationality'])
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $author['nationality'] }}
                </p>
                @endif
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    {{ $author['books_count'] ?? 0 }} libro(s)
                </p>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Botón "Ocultar autores" --}}
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
                Ocultar autores
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