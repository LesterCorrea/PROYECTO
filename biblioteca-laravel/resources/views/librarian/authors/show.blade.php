@extends('layouts.panel')

@section('title', $autore->name)
@section('page-title', 'Detalle de autor')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('librarian.autores.index') }}"
            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $autore->name }}</h2>
        <a href="{{ route('librarian.autores.edit', $autore) }}"
            class="ml-auto flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>
    </div>

    <div class="flex items-start gap-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
            <img src="{{ $autore->image_url }}" alt="{{ $autore->name }}" class="w-full h-full object-cover" />
        </div>
        <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $autore->name }}</h3>
            @if($autore->nationality)
            <p class="text-indigo-600 dark:text-indigo-400 text-sm font-medium mt-0.5">{{ $autore->nationality }}</p>
            @endif
            @if($autore->bio)
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-3 leading-relaxed">{{ $autore->bio }}</p>
            @endif
            <div class="flex gap-6 mt-4 text-sm">
                <div>
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $autore->books->count() }}</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Libros</p>
                </div>
                <div>
                    <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $autore->magazines->count() }}</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Revistas</p>
                </div>
            </div>
        </div>
    </div>

    @if($autore->books->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Libros</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($autore->books as $book)
            <div class="flex items-center gap-3 px-6 py-3">
                <div class="w-8 h-11 rounded overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $book->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $book->published_year ?? '—' }}</p>
                </div>
                <a href="{{ route('librarian.libros.show', $book) }}"
                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex-shrink-0">
                    Ver
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection