@props(['book', 'view' => 'grid'])

@if($view === 'grid')
<a href="{{ route('books.show', $book['isbn'] ?? $book->isbn ?? '#') }}"
    class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
    <div class="relative aspect-[2/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
        <img src="{{ isset($book['cover_image']) && $book['cover_image'] ? asset('storage/'.$book['cover_image']) : asset('images/default-cover.png') }}"
            alt="{{ $book['title'] ?? $book->title ?? '' }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy" />
        @if(isset($book['available_copies']) && $book['available_copies'] > 0)
        <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full font-medium">
            Disponible
        </span>
        @else
        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-medium">
            No disponible
        </span>
        @endif
    </div>
    <div class="p-3">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            {{ $book['title'] ?? $book->title ?? '' }}
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
            {{ $book['author']['name'] ?? ($book->author->name ?? '') }}
        </p>
        @if(isset($book['category']['color']))
        <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded-full font-medium"
            style="background-color: {{ $book['category']['color'] }}20; color: {{ $book['category']['color'] }}">
            {{ $book['category']['name'] ?? '' }}
        </span>
        @endif
    </div>
</a>
@else
{{-- Vista lista --}}
<a href="{{ route('books.show', $book['isbn'] ?? $book->isbn ?? '#') }}"
    class="group flex gap-4 bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
    <div class="w-16 aspect-[2/3] flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
        <img src="{{ isset($book['cover_image']) && $book['cover_image'] ? asset('storage/'.$book['cover_image']) : asset('images/default-cover.png') }}"
            alt="{{ $book['title'] ?? '' }}"
            class="w-full h-full object-cover"
            loading="lazy" />
    </div>
    <div class="flex-1 min-w-0">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            {{ $book['title'] ?? '' }}
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
            {{ $book['author']['name'] ?? '' }}
        </p>
        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
            {{ $book['description'] ?? '' }}
        </p>
        <div class="flex items-center gap-3 mt-2">
            @if(isset($book['available_copies']) && $book['available_copies'] > 0)
            <span class="text-xs text-green-600 dark:text-green-400 font-medium">✓ Disponible</span>
            @else
            <span class="text-xs text-red-500 font-medium">✗ No disponible</span>
            @endif
            @if(isset($book['category']['name']))
            <span class="text-xs text-gray-400">{{ $book['category']['name'] }}</span>
            @endif
        </div>
    </div>
</a>
@endif