@props(['title', 'items', 'type' => 'book'])

<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h2>
        <div class="flex gap-2">
            <button x-on:click="scrollLeft('{{ Str::slug($title) }}')"
                class="p-2 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button x-on:click="scrollRight('{{ Str::slug($title) }}')"
                class="p-2 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Skeleton mientras carga --}}
    @if(empty($items))
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @for($i = 0; $i
        < 6; $i++)
            <x-skeleton-card />
        @endfor
    </div>
    @else
    <div id="carousel-{{ Str::slug($title) }}"
        class="flex gap-4 overflow-x-auto scroll-smooth pb-3 snap-x snap-mandatory scrollbar-hide">
        @foreach($items as $item)
        @if($item !== null)
        <div class="flex-shrink-0 w-36 snap-start">
            @if($type === 'book')
            <a href="{{ route('books.show', $item->isbn ?? $item['isbn'] ?? '#') }}"
                class="group block">
                {{-- Portada --}}
                <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 mb-3 shadow-sm group-hover:shadow-md transition-shadow">
                    <img src="{{ $item->cover_url ?? asset('images/default-cover.png') }}"
                        alt="{{ $item->title ?? $item['title'] ?? '' }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy" />
                    {{-- Badge disponibilidad --}}
                    @if(isset($item->available_copies) && $item->available_copies > 0)
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                        Disponible
                    </span>
                    @endif
                </div>
                <h3 class="text-xs font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 mb-1">
                    {{ $item->title ?? $item['title'] ?? '' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
                    {{ $item->author->name ?? $item['author']['name'] ?? '' }}
                </p>
            </a>
            @elseif($type === 'magazine')
            <a href="{{ route('magazines.show', $item->id ?? $item['id'] ?? '#') }}"
                class="group block">
                <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 mb-3 shadow-sm group-hover:shadow-md transition-shadow">
                    <img src="{{ $item->cover_url ?? asset('images/default-cover.png') }}"
                        alt="{{ $item->title ?? '' }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy" />
                </div>
                <h3 class="text-xs font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 mb-1">
                    {{ $item->title ?? '' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Vol. {{ $item->volume ?? '-' }}
                </p>
            </a>
            @elseif($type === 'author')
            <a href="{{ route('authors.show', $item->id ?? '#') }}"
                class="group block text-center">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 mb-3 shadow-sm group-hover:shadow-md transition-shadow">
                    <img src="{{ $item->image_url ?? asset('images/default-author.png') }}"
                        alt="{{ $item->name ?? '' }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy" />
                </div>
                <h3 class="text-xs font-semibold text-gray-900 dark:text-gray-100 line-clamp-2">
                    {{ $item->name ?? '' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $item->nationality ?? '' }}
                </p>
            </a>
            @elseif($type === 'collection')
            <a href="{{ route('collections.show', $item->slug ?? '#') }}"
                class="group block">
                <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 mb-3 shadow-sm group-hover:shadow-md transition-shadow">
                    <img src="{{ isset($item->cover_image) && $item->cover_image ? asset('storage/'.$item->cover_image) : asset('images/default-cover.png') }}"
                        alt="{{ $item->name ?? '' }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-2">
                        <span class="text-white text-xs font-bold line-clamp-2">{{ $item->name ?? '' }}</span>
                    </div>
                </div>
            </a>
            @endif
        </div>
        @endif
        @endforeach
    </div>
    @endif
</section>