@extends('layouts.panel')

@section('title', 'Reporte de Usuarios')
@section('page-title', 'Reporte de Usuarios')

@section('content')
<div class="space-y-6">

    <div class="flex justify-end">
        <a href="{{ route('admin.reports.export', 'users') }}"
            class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar CSV
        </a>
    </div>

    {{-- Libros más prestados --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100">
                Top libros más prestados
            </h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($topBooks as $index => $book)
            <div class="flex items-center gap-4 px-6 py-3">
                <span class="w-7 h-7 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex-shrink-0">
                    {{ $index + 1 }}
                </span>
                <p class="flex-1 text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ $book['title'] ?? $book->title }}
                </p>
                <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                    {{ $book['loan_count'] ?? $book->loan_count }} préstamos
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tabla de usuarios --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100">Actividad por usuario</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Usuario</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Rol</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Préstamos</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Reservas</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Multas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($user['name'] ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $user['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user['email'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3 hidden md:table-cell">
                        @foreach($user['roles'] ?? [] as $role)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-medium">
                            {{ ucfirst($role['name']) }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-3 text-center font-semibold text-indigo-600 dark:text-indigo-400">
                        {{ $user['loans_count'] }}
                    </td>
                    <td class="px-6 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">
                        {{ $user['reservations_count'] }}
                    </td>
                    <td class="px-6 py-3 text-center font-semibold {{ $user['fines_count'] > 0 ? 'text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
                        {{ $user['fines_count'] }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        No hay usuarios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection