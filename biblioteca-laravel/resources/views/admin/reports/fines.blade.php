@extends('layouts.panel')

@section('title', 'Reporte de Multas')
@section('page-title', 'Reporte de Multas')

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
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Estado</label>
                <select name="status"
                    class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="all" {{ $status === 'all'     ? 'selected' : '' }}>Todas</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pendientes</option>
                    <option value="paid" {{ $status === 'paid'    ? 'selected' : '' }}>Pagadas</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                    Filtrar
                </button>
                <a href="{{ route('admin.reports.export', 'fines') }}"
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $summary['total_count'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total multas</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                ${{ number_format($summary['pending_amount'], 2) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pendiente</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                ${{ number_format($summary['paid_amount'], 2) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Recaudado</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                ${{ number_format($summary['total_amount'], 2) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total generado</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Usuario</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Libro</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Días</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Monto</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Estado</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden lg:table-cell">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($fines as $fine)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $fine['user']['name'] ?? '—' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $fine['user']['email'] ?? '' }}</p>
                    </td>
                    <td class="px-6 py-3 hidden md:table-cell text-gray-600 dark:text-gray-400 truncate max-w-xs">
                        {{ $fine['loan']['book']['title'] ?? '—' }}
                    </td>
                    <td class="px-6 py-3 text-center font-semibold text-red-500">
                        {{ $fine['overdue_days'] }}
                    </td>
                    <td class="px-6 py-3 text-right font-semibold text-gray-900 dark:text-gray-100">
                        ${{ number_format($fine['total_amount'], 2) }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                {{ $fine['status'] === 'paid'
                                    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                    : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ $fine['status'] === 'paid' ? 'Pagada' : 'Pendiente' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400 hidden lg:table-cell text-xs">
                        {{ isset($fine['created_at']) ? \Carbon\Carbon::parse($fine['created_at'])->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        No hay multas en este rango de fechas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection