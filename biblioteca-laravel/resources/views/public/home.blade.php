@extends('layouts.app')

@section('title', 'Inicio — Biblioteca')

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 dark:from-indigo-900 dark:via-indigo-800 dark:to-purple-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 leading-tight">
            Descubre tu próxima<br>
            <span class="text-indigo-200">lectura favorita</span>
        </h1>
        <p class="text-indigo-100 text-lg mb-8 max-w-xl mx-auto">
            Miles de libros y revistas al alcance de tu mano.
            Explora, reserva y lee desde cualquier lugar.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('books.index') }}"
                class="px-6 py-3 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 transition-colors shadow-lg">
                Explorar catálogo
            </a>
            @guest
            <a href="{{ route('register') }}"
                class="px-6 py-3 bg-indigo-500 text-white font-semibold rounded-xl hover:bg-indigo-400 transition-colors border border-indigo-400">
                Crear cuenta gratis
            </a>
            @endguest
        </div>

        {{-- Stats --}}
        <div class="flex justify-center gap-12 mt-12">
            <div class="text-center">
                <p class="text-3xl font-bold text-white">{{ $stats['total_books'] }}</p>
                <p class="text-indigo-200 text-sm mt-1">Libros</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-white">{{ $stats['total_magazines'] }}</p>
                <p class="text-indigo-200 text-sm mt-1">Revistas</p>
            </div>
        </div>
    </div>
</section>

{{-- Carruseles --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
    x-data="carouselScroll()">

    @forelse($featuredLists as $list)
    {{-- Detectar tipo del primer item --}}
    @php
    $firstItem = $list['items'][0] ?? null;
    $type = 'book';
    if ($firstItem) {
    if ($firstItem instanceof \App\Models\Magazine) $type = 'magazine';
    elseif ($firstItem instanceof \App\Models\Author) $type = 'author';
    elseif ($firstItem instanceof \App\Models\Collection) $type = 'collection';
    }
    @endphp

    <x-carousel
        :title="$list['title']"
        :items="$list['items']"
        :type="$type" />
    @empty
    {{-- Sin listas configuradas aún --}}
    <div class="text-center py-20">
        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400 mb-2">
            Aún no hay listas destacadas
        </h3>
        <p class="text-sm text-gray-400 dark:text-gray-500">
            El bibliotecario puede crear carruseles desde el panel de gestión.
        </p>
        @auth
        @role('librarian|admin')
        <a href="{{ route('librarian.carruseles.create') }}"
            class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            Crear primer carrusel
        </a>
        @endrole
        @endauth
    </div>
    @endforelse
</section>
@endsection

@push('scripts')
<script>
    function carouselScroll() {
        return {
            scrollLeft(id) {
                const el = document.getElementById('carousel-' + id);
                if (el) el.scrollBy({
                    left: -600,
                    behavior: 'smooth'
                });
            },
            scrollRight(id) {
                const el = document.getElementById('carousel-' + id);
                if (el) el.scrollBy({
                    left: 600,
                    behavior: 'smooth'
                });
            }
        };
    }
</script>
@endpush