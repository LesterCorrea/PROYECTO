@extends('layouts.panel')

@section('title', 'Nuevo Carrusel')
@section('page-title', 'Crear carrusel')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST"
            action="{{ route('librarian.carruseles.store') }}"
            class="space-y-6">
            @csrf

            {{-- Título --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Título <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="Ej: Libros más populares"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Descripción
                </label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Tipo de elementos --}}
            <div x-data="{ selected: '{{ old('type', 'books') }}' }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de elementos <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    Define qué tipo de contenido puede contener este carrusel.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                    ['books', 'Libros', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'indigo'],
                    ['magazines', 'Revistas', 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', 'blue'],
                    ['books_magazines', 'Libros y Revistas', 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z', 'purple'],
                    ['authors', 'Autores', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'emerald'],
                    ['collections', 'Colecciones / Sagas', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'amber'],
                    ] as [$value, $label, $icon, $color])
                    <label @click="selected = '{{ $value }}'"
                        class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                        :class="selected === '{{ $value }}'
                       ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 ring-2 ring-indigo-500/30'
                       : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/30'">

                        <input type="radio" name="type" value="{{ $value }}"
                            x-model="selected"
                            class="sr-only" />

                        {{-- Ícono --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors"
                            :class="selected === '{{ $value }}'
                         ? 'bg-{{ $color }}-500 shadow-lg shadow-{{ $color }}-500/30'
                         : 'bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30'">
                            <svg class="w-5 h-5 transition-colors"
                                :class="selected === '{{ $value }}'
                             ? 'text-white'
                             : 'text-{{ $color }}-600 dark:text-{{ $color }}-400'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $icon }}" />
                            </svg>
                        </div>

                        {{-- Label --}}
                        <span class="text-sm font-medium transition-colors"
                            :class="selected === '{{ $value }}'
                          ? 'text-indigo-700 dark:text-indigo-300'
                          : 'text-gray-700 dark:text-gray-300'">
                            {{ $label }}
                        </span>

                        {{-- Check de seleccionado --}}
                        <div class="ml-auto flex-shrink-0"
                            x-show="selected === '{{ $value }}'">
                            <div class="w-5 h-5 rounded-full bg-indigo-500 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('type')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Secciones donde aparece (múltiple) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Visible en las secciones <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    Selecciona una o más secciones donde aparecerá este carrusel.
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach([
                    'home' => 'Inicio',
                    'books' => 'Libros',
                    'magazines' => 'Revistas',
                    'collections' => 'Colecciones',
                    'authors' => 'Autores',
                    ] as $value => $label)
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <input type="checkbox" name="sections[]" value="{{ $value }}"
                            {{ in_array($value, old('sections', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('sections')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Orden y activo --}}
            <div class="flex items-center gap-4">
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Orden</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" />
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">
                        Visible en el sitio público
                    </label>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('librarian.carruseles.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Crear carrusel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection