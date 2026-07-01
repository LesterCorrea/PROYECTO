@extends('layouts.panel')

@section('title', 'Colecciones')
@section('page-title', 'Colecciones / Sagas')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $collections->total() }} colección(es)</p>
        <a href="{{ route('librarian.colecciones.create') }}"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva colección
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($collections as $collection)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="h-32 overflow-hidden bg-gray-100 dark:bg-gray-700">
                <img src="{{ $collection->cover_image ? asset('storage/'.$collection->cover_image) : asset('images/default-cover.png') }}"
                    alt="{{ $collection->name }}"
                    class="w-full h-full object-cover" />
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $collection->name }}</h3>
                @if($collection->description)
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                    {{ $collection->description }}
                </p>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">
                        {{ $collection->books_count }} libro(s)
                    </span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('librarian.colecciones.show', $collection) }}"
                            class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('librarian.colecciones.edit', $collection) }}"
                            class="p-1.5 text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form method="POST"
                            action="{{ route('librarian.colecciones.destroy', $collection) }}"
                            onsubmit="return confirm('Eliminar esta colección?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
            <p class="text-gray-400 dark:text-gray-500 mb-3">No hay colecciones registradas</p>
            <a href="{{ route('librarian.colecciones.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                Crear primera colección
            </a>
        </div>
        @endforelse
    </div>

    @if($collections->hasPages())
    <div class="mt-4">{{ $collections->links() }}</div>
    @endif
</div>
@endsection