@extends('layouts.panel')

@section('title', 'Nueva Colección')
@section('page-title', 'Nueva colección / saga')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST"
            action="{{ route('librarian.colecciones.store') }}"
            enctype="multipart/form-data"
            class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Imagen de portada <span class="text-gray-400 text-xs">(opcional)</span>
                </label>
                <input type="file" name="cover_image" accept="image/*"
                    class="w-full text-sm text-gray-600 dark:text-gray-400
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                              dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                              hover:file:bg-indigo-100 cursor-pointer" />
                @error('cover_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('librarian.colecciones.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Crear colección
                </button>
            </div>
        </form>
    </div>
</div>
@endsection