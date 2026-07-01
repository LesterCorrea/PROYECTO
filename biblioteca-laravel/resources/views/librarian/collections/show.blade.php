@extends('layouts.panel')

@section('title', $coleccione->name)
@section('page-title', 'Gestionar colección')

@section('content')
<div class="space-y-5 max-w-full">

    {{-- Cabecera --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('librarian.colecciones.index') }}"
        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $coleccione->name }}</h2>
        <a href="{{ route('librarian.colecciones.edit', $coleccione) }}"
        class="ml-auto flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar info
        </a>
    </div>

    {{-- Layout dos columnas --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- ═══ COLUMNA IZQUIERDA: libros en la saga ═══ --}}
        <div class="space-y-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                        Libros en la saga
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                            ({{ count($sagaBooks) }} libros · lista doblemente enlazada)
                        </span>
                    </h3>
                    @if(count($sagaBooks) > 1)
                        <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            Arrastra para reordenar
                        </span>
                    @endif
                </div>

                @if(empty($sagaBooks))
                    <div class="px-6 py-12 text-center">
                        <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/>
                        </svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500">
                            Busca y añade libros desde el panel derecho.
                        </p>
                    </div>
                @else
                    <div id="saga-sortable" class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($sagaBooks as $index => $book)
                            <div class="saga-item flex items-center gap-4 px-6 py-4
                                        hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors
                                        cursor-grab active:cursor-grabbing select-none"
                                data-id="{{ $book->id }}">

                                {{-- Handle --}}
                                <div class="drag-handle text-gray-300 dark:text-gray-600
                                            hover:text-gray-500 dark:hover:text-gray-400 transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8h16M4 16h16"/>
                                    </svg>
                                </div>

                                {{-- Número de orden --}}
                                <div class="order-badge w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30
                                            flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $book->saga_order ?? $index + 1 }}
                                    </span>
                                </div>

                                {{-- Portada --}}
                                <div class="w-10 h-14 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0 shadow-sm">
                                    <img src="{{ $book->cover_url }}"
                                        alt="{{ $book->title }}"
                                        class="w-full h-full object-cover"/>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $book->title }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5 text-xs">
                                        @if(!$book->previousBook)
                                            <span class="text-emerald-500 font-medium">Primer libro</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">
                                                ← {{ $book->previousBook->title }}
                                            </span>
                                        @endif
                                        @if(!$book->nextBook)
                                            <span class="text-indigo-500 font-medium">Último libro</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">
                                                → {{ $book->nextBook->title }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Quitar --}}
                                <form method="POST"
                                    action="{{ route('librarian.colecciones.removeBook', [$coleccione, $book]) }}"
                                    onsubmit="return confirm('Quitar este libro de la colección?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400
                                                hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    {{-- Indicador de guardado --}}
                    <div id="save-indicator"
                        class="hidden items-center justify-center gap-2 px-6 py-3
                                border-t border-gray-100 dark:border-gray-700
                                text-xs text-emerald-600 dark:text-emerald-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Orden guardado correctamente
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ COLUMNA DERECHA: buscador de libros ═══ --}}
        <div class="space-y-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Añadir Libros</h3>
                    <div class="relative">
                        <input type="text"
                                id="book-search"
                                placeholder="Buscar por título, ISBN..."
                                class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <div id="search-spinner"
                                class="hidden absolute right-3 top-3 w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin">
                        </div>
                    </div>
                </div>

                <div id="book-results"
                    class="max-h-[calc(100vh-280px)] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                        Escribe para buscar libros disponibles.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
// ── DRAG & DROP para reordenar la saga ─────────────────────────
(function () {
    const list = document.getElementById('saga-sortable');
    if (!list) return;

    const reorderUrl = '{{ route("librarian.colecciones.reorderBooks", $coleccione) }}';
    const csrf       = '{{ csrf_token() }}';
    const indicator  = document.getElementById('save-indicator');
    let saveTimer    = null;

    new Sortable(list, {
        animation:   200,
        handle:      '.drag-handle',
        ghostClass:  'sortable-ghost',
        chosenClass: 'sortable-chosen',
        easing:      'cubic-bezier(0.25, 1, 0.5, 1)',

        onEnd() {
            // Actualizar números de orden visualmente
            list.querySelectorAll('.saga-item').forEach((row, index) => {
                const badge = row.querySelector('.order-badge span');
                if (badge) badge.textContent = index + 1;
            });

            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveOrder, 600);
        }
    });

    async function saveOrder() {
        const rows  = list.querySelectorAll('.saga-item');
        const items = Array.from(rows).map(r => parseInt(r.dataset.id));

        try {
            const res = await fetch(reorderUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ items }),
            });

            if (res.ok) {
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
                clearTimeout(window._sagaSavedTimer);
                window._sagaSavedTimer = setTimeout(() => {
                    indicator.classList.add('hidden');
                    indicator.classList.remove('flex');
                }, 2500);
            }
        } catch (e) {
            console.error('Error al guardar orden saga:', e);
        }
    }
})();

// ── BUSCADOR de libros para añadir ─────────────────────────────
(function () {
    const input    = document.getElementById('book-search');
    const results  = document.getElementById('book-results');
    const spinner  = document.getElementById('search-spinner');
    const csrf     = '{{ csrf_token() }}';
    const addUrl   = '{{ route("librarian.colecciones.addBook", $coleccione) }}';
    const searchUrl= '{{ route("librarian.colecciones.searchBooks", $coleccione) }}';

    // IDs de libros ya en la saga
    const inSaga = new Set([
        @foreach($sagaBooks as $book)
            {{ $book->id }},
        @endforeach
    ]);

    let searchTimer = null;

    // Buscar al cargar (mostrar todos disponibles)
    search('');

    input.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => search(input.value), 400);
    });

    async function search(query) {
        spinner.classList.remove('hidden');

        try {
            const res  = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            render(data);
        } catch (e) {
            results.innerHTML = `<div class="px-5 py-6 text-center text-sm text-red-400">Error al buscar.</div>`;
        } finally {
            spinner.classList.add('hidden');
        }
    }

    function render(books) {
        if (!books.length) {
            results.innerHTML = `<div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No se encontraron libros disponibles.</div>`;
            return;
        }

        results.innerHTML = books.map(book => `
            <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <div class="w-10 h-14 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                    <img src="${book.cover}" alt="${book.title}" class="w-full h-full object-cover" loading="lazy"/>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">${book.title}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${book.author}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">${book.isbn}</p>
                </div>
                <div class="flex-shrink-0">
                    ${inSaga.has(book.id)
                        ? `<span class="flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium
                                        text-emerald-600 dark:text-emerald-400
                                        bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                En saga
                            </span>`
                        : `<button onclick="addBook(${book.id}, '${book.title.replace(/'/g, "\\'")}')"
                                    id="btn-${book.id}"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600
                                            hover:bg-indigo-700 text-white text-xs font-medium
                                            rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Añadir
                            </button>`
                    }
                </div>
            </div>
        `).join('');
    }

    window.addBook = async function(bookId, bookTitle) {
        const btn = document.getElementById(`btn-${bookId}`);
        if (btn) {
            btn.disabled    = true;
            btn.textContent = 'Añadiendo...';
        }

        // Calcular siguiente posición disponible
        const currentCount = document.querySelectorAll('.saga-item').length;

        try {
            const res = await fetch(addUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    book_id:    bookId,
                    saga_order: currentCount + 1,
                }),
            });

            if (res.ok || res.status === 302) {
                inSaga.add(bookId);
                // Recargar para ver el libro en la saga
                window.location.reload();
            } else {
                const data = await res.json().catch(() => ({}));
                alert(data.message || 'Error al añadir el libro.');
                if (btn) {
                    btn.disabled    = false;
                    btn.textContent = 'Añadir';
                }
            }
        } catch (e) {
            console.error('Error:', e);
            if (btn) {
                btn.disabled    = false;
                btn.textContent = 'Añadir';
            }
        }
    };
})();
</script>

<style>
.sortable-ghost  { opacity: 0.4; background-color: #e0e7ff; border-radius: 8px; }
.dark .sortable-ghost  { background-color: #312e81; }
.sortable-chosen { background-color: #f5f3ff; box-shadow: 0 4px 16px rgba(99,102,241,0.15); }
.dark .sortable-chosen { background-color: #1e1b4b; }
</style>
@endpush