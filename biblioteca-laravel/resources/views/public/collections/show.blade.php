@extends('layouts.app')

@section('title', $collection->name . ' — Biblioteca')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Inicio</a>
        <span>/</span>
        <a href="{{ route('collections.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Colecciones</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $collection->name }}</span>
    </nav>

    {{-- Cabecera de la colección --}}
    <div class="flex items-start gap-6 mb-10">
        <div class="w-32 aspect-[2/3] rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-lg flex-shrink-0">
            <img src="{{ $collection->cover_image ? asset('storage/'.$collection->cover_image) : asset('images/default-cover.png') }}"
                alt="{{ $collection->name }}"
                class="w-full h-full object-cover" />
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                {{ $collection->name }}
            </h1>
            @if($collection->description)
            <p class="text-gray-600 dark:text-gray-300 mb-4 max-w-2xl">
                {{ $collection->description }}
            </p>
            @endif
            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" />
                    </svg>
                    {{ count($sagaBooks) }} libro(s) en la saga
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ number_format($collection->views) }} vistas
                </span>
            </div>
        </div>
    </div>

    {{-- Libros de la saga (lista doblemente enlazada) --}}
    <section>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Libros de la saga
        </h2>

        @if(empty($sagaBooks))
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <p class="text-gray-400 dark:text-gray-500">Esta colección no tiene libros asignados aún.</p>
        </div>
        @else
        <div class="space-y-4">
            @foreach($sagaBooks as $index => $book)
            <div class="flex items-start gap-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors group">

                {{-- Número en la saga --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $book->saga_order ?? $index + 1 }}
                    </span>
                </div>

                {{-- Portada --}}
                <div class="w-16 aspect-[2/3] rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0 shadow-sm">
                    <img src="{{ $book->cover_url }}"
                        alt="{{ $book->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ $book->title }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                        {{ $book->author->name ?? '—' }}
                    </p>
                    @if($book->description)
                    <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                        {{ $book->description }}
                    </p>
                    @endif
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400 dark:text-gray-500">
                        @if($book->published_year)
                        <span>{{ $book->published_year }}</span>
                        @endif
                        @if($book->pages)
                        <span>{{ $book->pages }} páginas</span>
                        @endif
                        <span class="{{ $book->available_copies > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }} font-medium">
                            {{ $book->available_copies > 0 ? 'Disponible' : 'No disponible' }}
                        </span>
                    </div>
                </div>

                {{-- Navegación prev/next (lista doblemente enlazada) --}}
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <a href="{{ route('books.show', $book->isbn) }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-xl transition-colors">
                        Ver libro
                    </a>
                    <div class="flex items-center gap-1 text-xs text-gray-400">
                        @if($book->previousBook)
                        <a href="{{ route('books.show', $book->previousBook->isbn) }}"
                            class="p-1 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                            title="Anterior: {{ $book->previousBook->title }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @endif
                        @if($book->nextBook)
                        <a href="{{ route('books.show', $book->nextBook->isbn) }}"
                            class="p-1 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                            title="Siguiente: {{ $book->nextBook->title }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </section>
</div>
@endsection