<footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Sistema de Biblioteca
            </div>
            <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('books.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Libros</a>
                <a href="{{ route('magazines.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Revistas</a>
                <a href="{{ route('collections.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Colecciones</a>
                <a href="{{ route('authors.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Autores</a>
            </div>
            <p class="text-sm text-gray-400 dark:text-gray-500">
                © {{ date('Y') }} Biblioteca. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>