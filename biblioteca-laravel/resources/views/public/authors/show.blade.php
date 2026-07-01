@extends('layouts.app')

@section('title', $author->name . ' — Biblioteca')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Inicio</a>
        <span>/</span>
        <a href="{{ route('authors.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Autores</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $author->name }}</span>
    </nav>

    {{-- Cabecera del autor --}}
    <div class="flex flex-col sm:flex-row items-start gap-6 mb-10">
        <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-lg flex-shrink-0">
            <img src="{{ $author->image_url }}"
                alt="{{ $author->name }}"
                class="w-full h-full object-cover" />
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                {{ $author->name }}
            </h1>
            @if($author->nationality)
            <p class="text-indigo-600 dark:text-indigo-400 font-medium mb-3">
                {{ $author->nationality }}
            </p>
            @endif
            @if($author->bio)
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed max-w-2xl">
                {{ $author->bio }}
            </p>
            @endif
            <div class="flex items-center gap-6 mt-4 text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" />
                    </svg>
                    {{ count($books) }} libro(s)
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7" />
                    </svg>
                    {{ count($magazines) }} revista(s)
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ number_format($author->views) }} vistas
                </span>
            </div>
        </div>
    </div>

    {{-- Libros del autor --}}
    @if(count($books) > 0)
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5">Libros</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($books as $book)
            <a href="{{ route('books.show', $book['isbn'] ?? $book->isbn) }}"
                class="group">
                <div class="aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 mb-2 shadow-sm group-hover:shadow-md transition-shadow">
                    <img src="{{ isset($book['cover_image']) && $book['cover_image'] ? asset('storage/'.$book['cover_image']) : asset('images/default-cover.png') }}"
                        alt="{{ $book['title'] ?? $book->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy" />
                </div>
                <h3 class="text-xs font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $book['title'] ?? $book->title }}
                </h3>
                @if(isset($book['published_year']))
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $book['published_year'] }}</p>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Revistas del autor --}}
    @if(count($magazines) > 0)
    <section>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5">Revistas</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($magazines as $magazine)
            <a href="{{ route('magazines.show', $magazine['id'] ?? $magazine->id) }}"
                class="group">
                <div class="aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 mb-2 shadow-sm group-hover:shadow-md transition-shadow">
                    <img src="{{ isset($magazine['cover_image']) && $magazine['cover_image'] ? asset('storage/'.$magazine['cover_image']) : asset('images/default-cover.png') }}"
                        alt="{{ $magazine['title'] ?? $magazine->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy" />
                </div>
                <h3 class="text-xs font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $magazine['title'] ?? $magazine->title }}
                </h3>
            </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection