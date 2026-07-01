@extends('layouts.panel')

@section('title', 'Editar Autor')
@section('page-title', 'Editar autor')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST"
            action="{{ route('librarian.autores.update', $autore) }}"
            enctype="multipart/form-data"
            class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $autore->name) }}" required
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nacionalidad</label>
                    <input type="text" name="nationality" value="{{ old('nationality', $autore->nationality) }}"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Biografía</label>
                <textarea name="bio" rows="4"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('bio', $autore->bio) }}</textarea>
            </div>

            @if($autore->image)
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
                <img src="{{ $autore->image_url }}" alt="Foto actual"
                    class="w-16 h-16 rounded-full object-cover shadow-sm" />
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Foto actual</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Sube una nueva para reemplazarla</p>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nueva foto <span class="text-gray-400 text-xs">(opcional)</span>
                </label>
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm text-gray-600 dark:text-gray-400
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                              dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                              hover:file:bg-indigo-100 cursor-pointer" />
                @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('librarian.autores.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection