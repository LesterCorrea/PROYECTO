@extends('layouts.app')

@section('title', $magazine->title . ' — Biblioteca')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Inicio</a>
        <span>/</span>
        <a href="{{ route('magazines.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Revistas</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-gray-100 font-medium truncate max-w-xs">{{ $magazine->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

        {{-- Portada --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24">
                <div class="aspect-[2/3] rounded-2xl overflow-hidden shadow-xl bg-gray-100 dark:bg-gray-800 mb-4">
                    <img src="{{ $magazine->cover_url }}"
                        alt="{{ $magazine->title }}"
                        class="w-full h-full object-cover" />
                </div>

                {{-- Progreso de lectura --}}
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
                </div>
                @endif
                @endauth

                {{-- Acciones --}}
                <div class="space-y-3">
                    @auth
                    @can('read pdf')
                    <a href="{{ route('pdf.reader', ['type' => 'revista', 'id' => $magazine->id]) }}"
                        target="_blank"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" />
                        </svg>
                        Leer revista
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

                    {{-- Reservar revista --}}
                    @auth
                    @role('student')
                    @if($magazine->isAvailable())
                    <form method="POST"
                        action="{{ route('student.reservations.store.magazine', $magazine) }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Reservar revista
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
            @if($magazine->category)
            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-3"
                style="background-color: {{ $magazine->category->color }}20; color: {{ $magazine->category->color }}">
                {{ $magazine->category->name }}
            </span>
            @endif

            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight">
                {{ $magazine->title }}
            </h1>

            {{-- Autores (múltiples) --}}
            @if($magazine->authors->count() > 0)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($magazine->authors as $author)
                <a href="{{ route('authors.show', $author) }}"
                    class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    <div class="w-6 h-6 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                        <img src="{{ $author->image_url }}"
                            alt="{{ $author->name }}"
                            class="w-full h-full object-cover" />
                    </div>
                    {{ $author->name }}
                    @if($author->pivot->role !== 'Autor')
                    <span class="text-xs text-gray-400">({{ $author->pivot->role }})</span>
                    @endif
                </a>
                @endforeach
            </div>
            @endif

            {{-- Rating --}}
            @php
            $avgRating = $magazine->comments->avg('rating');
            $totalComments = $magazine->comments->count();
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

            @if($magazine->description)
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                {{ $magazine->description }}
            </p>
            @endif

            {{-- Detalles --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                @if($magazine->issn)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ISSN</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $magazine->issn }}</p>
                </div>
                @endif
                @if($magazine->volume)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Volumen</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $magazine->volume }}</p>
                </div>
                @endif
                @if($magazine->issue_number)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Número</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $magazine->issue_number }}</p>
                </div>
                @endif
                @if($magazine->published_date)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Publicación</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $magazine->published_date->format('d/m/Y') }}
                    </p>
                </div>
                @endif
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Idioma</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $magazine->language }}</p>
                </div>
                @if($magazine->publisher)
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Editorial</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $magazine->publisher->name }}</p>
                </div>
                @endif
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Disponibles</p>
                    <p class="text-sm font-semibold
                        {{ $magazine->available_copies > 0
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : 'text-red-500' }}">
                        {{ $magazine->available_copies }} / {{ $magazine->total_copies }}
                    </p>
                </div>
            </div>

            {{-- Métricas --}}
            <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400 pb-6 border-b border-gray-200 dark:border-gray-700">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ number_format($magazine->views) }} vistas
                </span>
            </div>
        </div>
    </div>

    {{-- Comentarios --}}
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Comentarios y reseñas
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                ({{ $magazine->comments->count() }})
            </span>
        </h2>

        @auth
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Escribe tu reseña</h3>
            <form method="POST"
                action="{{ route('comments.store', ['type' => 'revista', 'id' => $magazine->id]) }}">
                @csrf
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
                <textarea name="content" rows="3" required
                    placeholder="¿Qué te pareció esta revista?"
                    class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
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
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Inicia sesión para dejar tu reseña</p>
            <a href="{{ route('login') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Iniciar sesión
            </a>
        </div>
        @endauth

        @forelse($magazine->comments as $comment)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 mb-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
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
                    @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->hasRole(['admin', 'librarian'])))
                    <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Eliminar este comentario?')"
                            class="text-xs text-red-400 hover:text-red-600 transition-colors">
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
            <p class="text-gray-400 dark:text-gray-500 text-sm">Aún no hay reseñas.</p>
        </div>
        @endforelse
    </section>
</div>
@endsection