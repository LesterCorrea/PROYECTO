@extends('layouts.panel')

@section('title', 'Multas')
@section('page-title', 'Gestión de Multas')

@section('content')
<div class="space-y-4">

    {{-- Total pendiente --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total multas pendientes</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                ${{ number_format($totalPending, 2) }}
            </p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Estudiante</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Libro</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Días</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Monto</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Estado</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($fines as $fine)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $fine->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $fine->user->email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-900 dark:text-gray-100 truncate max-w-xs">
                            {{ $fine->loan->book->title ?? '—' }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-semibold text-red-500">{{ $fine->overdue_days }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            ${{ number_format($fine->total_amount, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                {{ $fine->status === 'paid'
                                    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                    : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ $fine->status === 'paid' ? 'Pagada' : 'Pendiente' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end">
                            @if($fine->status === 'pending')
                            <form method="POST"
                                action="{{ route('librarian.fines.pay', $fine) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    Marcar pagada
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400">
                                {{ $fine->paid_at?->format('d/m/Y') }}
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        No hay multas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($fines->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $fines->links() }}
        </div>
        @endif
    </div>
</div>
@endsection