@extends('layouts.panel')

@section('title', 'Editar Categoría')
@section('page-title', 'Editar categoría')

@section('content')
<div class="max-w-lg">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST"
            action="{{ route('librarian.categorias.update', $categoria) }}"
            class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $categoria->name) }}" required
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Color <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" value="{{ old('color', $categoria->color) }}"
                        class="w-12 h-10 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" />
                    <input type="text" id="color-text" value="{{ old('color', $categoria->color) }}"
                        class="flex-1 px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                        readonly />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tipo <span class="text-red-500">*</span>
                </label>
                <select name="type" required
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="book" {{ old('type', $categoria->type) === 'book'     ? 'selected' : '' }}>Solo libros</option>
                    <option value="magazine" {{ old('type', $categoria->type) === 'magazine' ? 'selected' : '' }}>Solo revistas</option>
                    <option value="both" {{ old('type', $categoria->type) === 'both'     ? 'selected' : '' }}>Ambos</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('librarian.categorias.index') }}"
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

@push('scripts')
<script>
    const colorInput = document.querySelector('input[type="color"]');
    const colorText = document.getElementById('color-text');
    colorInput.addEventListener('input', () => {
        colorText.value = colorInput.value;
    });
</script>
@endpush
@endsection