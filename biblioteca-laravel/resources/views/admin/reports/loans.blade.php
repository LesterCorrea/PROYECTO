@extends('layouts.panel')

@section('title', 'Reporte de Préstamos')
@section('page-title', 'Reporte de Préstamos')

@section('content')
<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $from }}"
                    class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $to }}"
                    class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ordenar por</label>
                <select name="ordenar"
                    class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="loan_date" {{ $sortBy === 'loan_date'   ? 'selected' : '' }}>Fecha préstamo</option>
                    <option value="due_date" {{ $sortBy === 'due_date'    ? 'selected' : '' }}>Fecha vencimiento</option>
                    <option value="return_date" {{ $sortBy === 'return_date' ? 'selected' : '' }}>Fecha devolución</option>
                    <option value="status" {{ $sortBy === 'status'      ? 'selected' : '' }}>Estado</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                    Filtrar
                </button>
                <a href="{{ route('admin.reports.export', 'loans') }}"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar CSV
                </a>
            </div>
        </form>
    </div>

    {{-- Resumen --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
        ['Total', $summary['total'], 'gray'],
        ['Activos', $summary['active'], 'blue'],
        ['Devueltos', $summary['returned'], 'emerald'],
        ['Vencidos', $summary['overdue'], 'red'],
        ] as [$label, $value, $color])
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ $value }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Libro</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Usuario</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Préstamo</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Vence</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden lg:table-cell">Devuelto</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($loans as $loan)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3">
                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate max-w-xs">
                            {{ $loan['book']['title'] ?? '—' }}
                        </p>
                    </td>
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                        {{ $loan['user']['name'] ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell">
                        {{ isset($loan['loan_date']) ? \Carbon\Carbon::parse($loan['loan_date'])->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-6 py-3 hidden md:table-cell">
                        <span class="{{ isset($loan['due_date']) && \Carbon\Carbon::parse($loan['due_date'])->isPast() && $loan['status'] !== 'returned' ? 'text-red-500 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ isset($loan['due_date']) ? \Carbon\Carbon::parse($loan['due_date'])->format('d/m/Y') : '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell">
                        {{ isset($loan['return_date']) && $loan['return_date'] ? \Carbon\Carbon::parse($loan['return_date'])->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                {{ $loan['status'] === 'returned' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : '' }}
                                {{ $loan['status'] === 'active'   ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                                {{ $loan['status'] === 'overdue'  ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}">
                            {{ match($loan['status']) {
                                    'returned' => 'Devuelto',
                                    'active'   => 'Activo',
                                    'overdue'  => 'Vencido',
                                    default    => $loan['status'],
                                } }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        No hay préstamos en este rango de fechas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection