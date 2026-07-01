@extends('layouts.panel')

@section('title', $libro->title)
@section('page-title', 'Detalle del libro')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('librarian.libros.index') }}"
            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="font-bold text-gray-900 dark:text-gray-100 text-lg truncate">{{ $libro->title }}</h2>
        <a href="{{ route('librarian.libros.edit', $libro) }}"
            class="ml-auto flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Portada --}}
        <div class="md:col-span-1">
            <div class="aspect-[2/3] rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-lg">
                <img src="{{ $libro->cover_url }}"
                    alt="{{ $libro->title }}"
                    class="w-full h-full object-cover" />
            </div>
        </div>

        {{-- Info --}}
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    Información general
                </h3>
                <dl class="space-y-2">
                    @foreach([
                    ['ISBN', $libro->isbn],
                    ['Autor', $libro->author?->name ?? '—'],
                    ['Categoría', $libro->category?->name ?? '—'],
                    ['Editorial', $libro->publisher?->name ?? '—'],
                    ['Colección', $libro->collection?->name ?? '—'],
                    ['Año', $libro->published_year ?? '—'],
                    ['Páginas', $libro->pages ? $libro->pages . ' páginas' : '—'],
                    ['Idioma', $libro->language],
                    ] as [$label, $value])
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $libro->available_copies }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Disponibles</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $libro->total_copies }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total copias</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $libro->loan_count }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Préstamos</p>
                </div>
            </div>
        </div>
    </div>

    @if($libro->description)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
            Descripción
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
            {{ $libro->description }}
        </p>
    </div>
    @endif
</div>
@endsection