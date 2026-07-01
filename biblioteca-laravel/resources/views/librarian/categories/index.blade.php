    @extends('layouts.panel')

    @section('title', 'Categorías')
    @section('page-title', 'Categorías')

    @section('content')
    <div class="space-y-4">

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $categories->total() }} categoría(s)</p>
            <a href="{{ route('librarian.categorias.create') }}"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva categoría
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Categoría</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Tipo</th>
                        <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Libros</th>
                        <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Revistas</th>
                        <th class="text-right px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full flex-shrink-0"
                                    style="background-color: {{ $category->color }}"></div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $category->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                {{ match($category->type) {
                                    'book'     => 'Solo libros',
                                    'magazine' => 'Solo revistas',
                                    'both'     => 'Ambos',
                                    default    => $category->type,
                                } }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ $category->books_count }}
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">
                            {{ $category->magazines_count }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('librarian.categorias.edit', $category) }}"
                                    class="p-1.5 text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form method="POST"
                                    action="{{ route('librarian.categorias.destroy', $category) }}"
                                    onsubmit="return confirm('Eliminar esta categoría?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                            No hay categorías registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
    @endsection