@extends('layouts.panel')

@section('title', 'Gestionar Carrusel')
@section('page-title', 'Gestionar items del carrusel')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6"
    x-data="carouselManager({
         searchUrl: '{{ route('librarian.carruseles.search', $carrusele) }}',
         addUrl:    '{{ route('librarian.carruseles.addItem', $carrusele) }}',
         csrf:      '{{ csrf_token() }}'
     })">

    {{-- ═══ COLUMNA IZQUIERDA: info + items actuales ═══ --}}
    <div class="space-y-5">

        {{-- Info del carrusel --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $carrusele->title }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        {{-- Tipo --}}
                        <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-medium">
                            {{ $carrusele->type_label }}
                        </span>
                        {{-- Secciones --}}
                        @foreach($carrusele->sections ?? [] as $section)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-medium">
                            {{ ['home'=>'Inicio','books'=>'Libros','magazines'=>'Revistas','collections'=>'Colecciones','authors'=>'Autores'][$section] ?? $section }}
                        </span>
                        @endforeach
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            · {{ $carrusele->items->count() }} item(s)
                            · Lista circular doblemente enlazada
                        </span>
                    </div>
                </div>
                <a href="{{ route('librarian.carruseles.edit', $carrusele) }}"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar info
                </a>
            </div>
        </div>

        {{-- Items actuales del carrusel --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Items en el carrusel</h3>
                @if($carrusele->items->count() > 1)
                <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    Arrastra para reordenar
                </span>
                @endif
            </div>

            @if($carrusele->items->isEmpty())
            <div class="px-5 py-12 text-center">
                <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    Busca y añade elementos desde el panel derecho.
                </p>
            </div>
            @else
            {{-- Lista con drag and drop --}}
            <div id="sortable-items" class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($carrusele->items as $item)
                <div class="sortable-item flex items-center gap-3 px-5 py-3
                            hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors
                            cursor-grab active:cursor-grabbing select-none"
                    data-id="{{ $item->id }}">

                    {{-- Handle de arrastre --}}
                    <div class="drag-handle flex-shrink-0 text-gray-300 dark:text-gray-600
                                hover:text-gray-500 dark:hover:text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 8h16M4 16h16" />
                        </svg>
                    </div>

                    {{-- Número de orden --}}
                    <span class="order-badge w-7 h-7 flex items-center justify-center rounded-lg
                                 bg-indigo-100 dark:bg-indigo-900/30
                                 text-indigo-600 dark:text-indigo-400
                                 text-xs font-bold flex-shrink-0">
                        {{ $item->order }}
                    </span>

                    {{-- Portada/imagen --}}
                    <div class="w-9 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                        @if($item->itemable)
                        <img src="{{ $item->itemable->cover_url
                                      ?? $item->itemable->image_url
                                      ?? asset('images/default-cover.png') }}"
                            alt=""
                            class="w-full h-full object-cover" />
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $item->itemable?->title
                            ?? $item->itemable?->name
                            ?? 'Elemento eliminado' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ class_basename($item->itemable_type) }} · ID {{ $item->itemable_id }}
                        </p>
                    </div>

                    {{-- Eliminar --}}
                    <form method="POST"
                        action="{{ route('librarian.carruseles.removeItem', [$carrusele, $item]) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Quitar este elemento del carrusel?')"
                            class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400
                                    hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>

            {{-- se elimino hidden --}}
            {{-- Indicador de guardado --}}
            <div id="save-indicator"
                class="flex items-center justify-center gap-2 px-5 py-3
                    border-t border-gray-100 dark:border-gray-700
                    text-xs text-emerald-600 dark:text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Orden guardado correctamente
            </div>
            @endif
        </div>
    </div>

    {{-- ═══ COLUMNA DERECHA: buscador + elementos disponibles ═══ --}}
    <div class="space-y-5">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Añadir {{ $carrusele->type_label }}
                </h3>

                {{-- Buscador --}}
                <div class="relative">
                    <input type="text"
                        x-model="query"
                        @input.debounce.400ms="search()"
                        placeholder="Buscar por título{{ in_array($carrusele->type, ['books', 'books_magazines']) ? ', ISBN' : '' }}..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                    <div x-show="searching"
                        class="absolute right-3 top-3 w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>

            {{-- Resultados --}}
            <div class="max-h-[calc(100vh-280px)] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">

                {{-- Estado vacío inicial --}}
                <div x-show="!searching && results.length === 0 && query === ''"
                    class="px-5 py-10 text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        Escribe para buscar {{ strtolower($carrusele->type_label) }}.
                    </p>
                    <button @click="search()" type="button"
                        class="mt-2 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        Ver todos
                    </button>
                </div>

                {{-- Sin resultados --}}
                <div x-show="!searching && results.length === 0 && query !== ''"
                    class="px-5 py-10 text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        No se encontraron resultados para "<span class="font-medium" x-text="query"></span>".
                    </p>
                </div>

                {{-- Lista de resultados --}}
                <template x-for="item in results" :key="item.type + '_' + item.id">
                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                        {{-- Portada --}}
                        <div class="w-10 h-14 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                            <img :src="item.cover" :alt="item.title"
                                class="w-full h-full object-cover"
                                loading="lazy" />
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate" x-text="item.title"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="item.subtitle"></p>
                            <p x-show="item.isbn" class="text-xs text-gray-400 dark:text-gray-500 font-mono" x-text="item.isbn"></p>
                        </div>

                        {{-- Botón añadir / ya en carrusel --}}
                        <div class="flex-shrink-0">
                            <template x-if="item.in_carousel">
                                <span class="flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Añadido 
                                </span>
                            </template>
                            <template x-if="!item.in_carousel">
                                <button type="button"
                                    @click="addItem(item)"
                                    :disabled="adding === item.type + '_' + item.id"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors disabled:opacity-60">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span x-text="adding === item.type + '_' + item.id ? 'Añadiendo...' : 'Añadir'"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
    // ── Drag and drop para reordenar items ─────────────────────────
    (function() {
        const list = document.getElementById('sortable-items');
        if (!list) return;

        const reorderUrl = '{{ route('librarian.carruseles.reorder', $carrusele) }}';
        const csrf = '{{ csrf_token() }}';
        const indicator = document.getElementById('save-indicator');

        let saveTimer = null;

        const sortable = new Sortable(list, {
            animation: 200,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            easing: 'cubic-bezier(0.25, 1, 0.5, 1)',

            onEnd() {
                // Actualizar los badges de número de orden visualmente
                const rows = list.querySelectorAll('.sortable-item');
                rows.forEach((row, index) => {
                    const badge = row.querySelector('.order-badge');
                    if (badge) badge.textContent = index + 1;
                });

                // Guardar con debounce de 600ms
                clearTimeout(saveTimer);
                saveTimer = setTimeout(() => saveOrder(), 600);
            }
        });

        async function saveOrder() {
            const rows = list.querySelectorAll('.sortable-item');
            const items = Array.from(rows).map(row => parseInt(row.dataset.id));

            try {
                const res = await fetch(reorderUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        items
                    }),
                });

                if (res.ok) {
                    showSaved();
                }
            } catch (e) {
                console.error('Error al guardar orden:', e);
            }
        }

        function showSaved() {
            if (!indicator) return;
            indicator.classList.remove('hidden');
            indicator.classList.add('flex');
            clearTimeout(window._savedTimer);
            window._savedTimer = setTimeout(() => {
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
            }, 2500);
        }
    })();

    // ── Función del buscador y añadir items ────────────────────────
    function carouselManager(config) {
        return {
            query: '',
            results: [],
            searching: false,
            adding: null,
            csrf: config.csrf,

            init() {
                this.search();
            },

            async search() {
                this.searching = true;
                try {
                    const url = config.searchUrl + '?q=' + encodeURIComponent(this.query);
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    this.results = await res.json();
                } catch (e) {
                    this.results = [];
                } finally {
                    this.searching = false;
                }
            },

            async addItem(item) {
                const key = item.type + '_' + item.id;
                this.adding = key;

                try {
                    const res = await fetch(config.addUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify({
                            itemable_type: item.type,
                            itemable_id: item.id,
                        }),
                    });

                    if (res.ok) {
                        // Marcar como añadido en la lista
                        this.results = this.results.map(r =>
                            (r.type === item.type && r.id === item.id) ?
                            {
                                ...r,
                                in_carousel: true
                            } :
                            r
                        );

                        // Recargar columna izquierda
                        window.location.reload();
                    }
                } catch (e) {
                    console.error('Error al añadir:', e);
                } finally {
                    this.adding = null;
                }
            }
        };
    }
</script>

{{-- Estilos del drag and drop --}}
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #e0e7ff;
        border-radius: 8px;
    }

    .dark .sortable-ghost {
        background-color: #312e81;
    }

    .sortable-chosen {
        background-color: #f5f3ff;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.15);
    }

    .dark .sortable-chosen {
        background-color: #1e1b4b;
    }

    .sortable-drag {
        opacity: 1 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }
</style>
@endpush