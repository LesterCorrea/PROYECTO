@extends('layouts.panel')

@section('title', 'Carruseles')
@section('page-title', 'Gestión de Carruseles')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $lists->count() }} carrusel(es) configurado(s)
        </p>
        <a href="{{ route('librarian.carruseles.create') }}"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo carrusel
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($lists as $list)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $list->title }}
                    </h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-medium">
                            {{ $list->type_label }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $list->section_labels }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $list->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }} font-medium">
                            {{ $list->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-300 dark:text-gray-600 flex-shrink-0">
                    {{ $list->items_count }}
                </span>
            </div>

            @if($list->description)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2">
                {{ $list->description }}
            </p>
            @endif

            <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('librarian.carruseles.show', $list) }}"
                    class="flex-1 text-center px-3 py-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-medium rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors">
                    Gestionar items
                </a>
                <a href="{{ route('librarian.carruseles.edit', $list) }}"
                    class="p-2 text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
                <form method="POST"
                    action="{{ route('librarian.carruseles.destroy', $list) }}"
                    onsubmit="return confirm('Eliminar este carrusel y todos sus items?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 mb-4">No hay carruseles creados todavía</p>
            <a href="{{ route('librarian.carruseles.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                Crear primer carrusel
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection