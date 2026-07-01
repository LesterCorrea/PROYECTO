@extends('layouts.app')

@section('title', $book->title . ' — Biblioteca')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Inicio</a>
        <span>/</span>
        <a href="{{ route('books.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Libros</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-gray-100 font-medium truncate max-w-xs">{{ $book->title }}</span>
    </nav>

    {{-- Info principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

        {{-- Portada --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24">
                <div class="aspect-[2/3] rounded-2xl overflow-hidden shadow-xl bg-gray-100 dark:bg-gray-800 mb-4">
                    <img src="{{ $book->cover_url }}"
                        alt="{{ $book->title }}"
                        class="w-full h-full object-cover" />
                </div>

                {{-- Barra de progreso de lectura --}}
                @auth
                @if($readingProgress)
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Progreso de lectura</span>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                            {{ number_format($readingProgress->percentage, 0) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                            style="width: {{ $readingProgress->percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Página {{ $readingProgress->current_page }} de {{ $readingProgress->total_pages }}
                    </p>
                </div>
                @endif
                @endauth

                {{-- Acciones --}}
                <div class="space-y-3">
                    {{-- Leer PDF --}}
                    @auth
                    @can('read pdf')
                    <a href="{{ route('pdf.reader', ['type' => 'libro', 'id' => $book->id]) }}"
                        target="_blank"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" />
                        </svg>
                        Leer libro
                    </a>
                    @endcan
                    @else
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Inicia sesión para leer
                    </a>
                    @endauth

                    {{-- Reservar --}}
                    @auth
                    @role('student')
                    @if($book->isAvailable())
                    <form method="POST"
                        action="{{ route('student.reservations.store', $book) }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Reservar libro
                        </button>
                    </form>
                    @else
                    <button disabled
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-semibold rounded-xl cursor-not-allowed">
                        Sin copias disponibles
                    </button>
                    @endif
                    @endrole
                    @else
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Inicia sesión para reservar
                    </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Información --}}
        <div class="lg:col-span-2">
            {{-- Categoría badge --}}
            @if($book->category)
            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-3"
                style="background-color: {{ $book->category->color }}20; color: {{ $book->category->color }}">
                {{ $book->category->name }}
            </span>
            @endif

            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight">
                {{ $book->title }}
            </h1>

            {{-- Autor --}}
            @if($book->author)
            <a href="{{ route('authors.show', $book->author) }}"
                class="text-lg text-indigo-600 dark:text-indigo-400 hover:underline font-medium mb-4 inline-block">
                {{ $book->author->name }}
            </a>
            @endif

            {{-- Rating promedio --}}
            @php
            $avgRating = $book->comments->avg('rating');
            $totalComments = $book->comments->count();
            @endphp
            @if($totalComments > 0)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        @endfor
                </div>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ number_format($avgRating, 1) }} ({{ $totalComments }} reseñas)
                </span>
            </div>
            @endif

            {{-- Descripción --}}
            @if($book->description)
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                {{ $book->description }}
            </p>
            @endif

            {{-- Detalles --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                @if($book->published_year)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Año</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $book->published_year }}</p>
                </div>
                @endif
                @if($book->pages)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Páginas</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $book->pages }}</p>
                </div>
                @endif
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Idioma</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $book->language }}</p>
                </div>
                @if($book->isbn)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ISBN</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $book->isbn }}</p>
                </div>
                @endif
                @if($book->publisher)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Editorial</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $book->publisher->name }}</p>
                </div>
                @endif
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Disponibles</p>
                    <p class="text-sm font-semibold {{ $book->available_copies > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                        {{ $book->available_copies }} / {{ $book->total_copies }}
                    </p>
                </div>
            </div>

            {{-- Métricas --}}
            <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400 pb-6 border-b border-gray-200 dark:border-gray-700">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ number_format($book->views) }} vistas
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" />
                    </svg>
                    {{ number_format($book->loan_count) }} préstamos
                </span>
            </div>
        </div>
    </div>

    {{-- Navegación de saga (lista doblemente enlazada) --}}
    @if($sagaList && count($sagaList) > 1)
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
            Saga: {{ $book->collection->name ?? 'Colección' }}
        </h2>

        {{-- Navegación prev/next --}}
        @if($sagaNeighbors['prev'] || $sagaNeighbors['next'])
        <div class="flex gap-4 mb-6">
            @if($sagaNeighbors['prev'])
            <a href="{{ route('books.show', $sagaNeighbors['prev']->isbn) }}"
                class="flex items-center gap-3 flex-1 bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Anterior</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        {{ $sagaNeighbors['prev']->title }}
                    </p>
                </div>
            </a>
            @else
            <div class="flex-1"></div>
            @endif

            @if($sagaNeighbors['next'])
            <a href="{{ route('books.show', $sagaNeighbors['next']->isbn) }}"
                class="flex items-center gap-3 flex-1 bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors group justify-end text-right">
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Siguiente</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        {{ $sagaNeighbors['next']->title }}
                    </p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            @endif
        </div>
        @endif

        <!-- Gap a 5, w a 32,  -->
        {{-- Todos los libros de la saga --}}
        <!-- <div class="flex gap-5 overflow-x-auto pb- scrollbar-hide" style="border: 2px solid black;"> -->
        <div class="flex gap-5 overflow-x-auto pt-4 px-4 pb-2 scrollbar-hide" style="border: 2px solid black;">
            @foreach($sagaList as $sagaBook)
            <a href="{{ route('books.show', $sagaBook->isbn) }}"
                class="flex-shrink-0 w-32 group">
                <div class="aspect-[2/3] rounded-lg overflow-hidden mb-2 ring-2 {{ $sagaBook->id === $book->id ? 'ring-indigo-500' : 'ring-transparent' }} transition-all">
                    <img src="{{ $sagaBook->cover_url }}"
                        alt="{{ $sagaBook->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                </div>
                <p class="text-xs text-center font-medium text-gray-700 dark:text-gray-300 line-clamp-2
                                  {{ $sagaBook->id === $book->id ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                    {{ $sagaBook->title }}
                </p>
                @if($sagaBook->saga_order)
                <p class="text-xs text-center text-gray-400 dark:text-gray-500">
                    #{{ $sagaBook->saga_order }}
                </p>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Sección de comentarios --}}
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Comentarios y reseñas
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                ({{ $book->comments->count() }})
            </span>
        </h2>

        {{-- Formulario de comentario --}}
        @auth
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Escribe tu reseña</h3>
            <form method="POST"
                action="{{ route('comments.store', ['type' => 'libro', 'id' => $book->id]) }}">
                @csrf

                {{-- Calificación --}}
                <div class="mb-4" x-data="{ rating: 0, hover: 0 }">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Calificación</p>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                            @click="rating = {{ $i }}"
                            @mouseenter="hover = {{ $i }}"
                            @mouseleave="hover = 0"
                            class="w-8 h-8 transition-colors">
                            <svg :class="(hover || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            </button>
                            @endfor
                    </div>
                    <input type="hidden" name="rating" :value="rating" />
                </div>

                {{-- Contenido --}}
                <textarea name="content" rows="3" required
                    placeholder="¿Qué te pareció este libro?"
                    class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>

                @error('content')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <div class="flex justify-end mt-3">
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Publicar reseña
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-gray-600 p-6 mb-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                Inicia sesión para dejar tu reseña
            </p>
            <a href="{{ route('login') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Iniciar sesión
            </a>
        </div>
        @endauth

        {{-- Lista de comentarios --}}
        @forelse($book->comments as $comment)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 mb-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    {{-- Avatar --}}
                    <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $comment->user->name }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $comment->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Estrellas --}}
                    @if($comment->rating)
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $comment->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            @endfor
                    </div>
                    @endif

                    {{-- Eliminar (propio usuario o admin/librarian) --}}
                    @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->hasRole(['admin', 'librarian'])))
                    <form method="POST"
                        action="{{ route('comments.destroy', $comment) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('¿Eliminar este comentario?')"
                            class="text-xs text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors">
                            Eliminar
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300 mt-3 leading-relaxed">
                {{ $comment->content }}
            </p>
        </div>
        @empty
        <div class="text-center py-10">
            <p class="text-gray-400 dark:text-gray-500 text-sm">
                Aún no hay reseñas. ¡Sé el primero en opinar!
            </p>
        </div>
        @endforelse
    </section>
</div>
@endsection