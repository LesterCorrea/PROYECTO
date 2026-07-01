@extends('layouts.panel')

@section('title', $revista->title)
@section('page-title', 'Detalle de revista')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('librarian.revistas.index') }}"
            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="font-bold text-gray-900 dark:text-gray-100 text-lg truncate">{{ $revista->title }}</h2>
        <a href="{{ route('librarian.revistas.edit', $revista) }}"
            class="ml-auto flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="aspect-[2/3] rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-lg">
                <img src="{{ $revista->cover_url }}" alt="{{ $revista->title }}" class="w-full h-full object-cover" />
            </div>
        </div>
        <div class="md:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Información</h3>
                <dl class="space-y-2">
                    @foreach([
                    ['ISSN', $revista->issn ?? '—'],
                    ['Categoría', $revista->category?->name ?? '—'],
                    ['Editorial', $revista->publisher?->name ?? '—'],
                    ['Volumen', $revista->volume ?? '—'],
                    ['Número', $revista->issue_number ?? '—'],
                    ['Publicación', $revista->published_date?->format('d/m/Y') ?? '—'],
                    ['Idioma', $revista->language],
                    ['Vistas', number_format($revista->views)],
                    ] as [$label, $value])
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 mt-4">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Autores</h3>
                <div class="space-y-2">
                    @foreach($revista->authors as $author)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                            <img src="{{ $author->image_url }}" alt="{{ $author->name }}" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $author->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $author->pivot->role }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection