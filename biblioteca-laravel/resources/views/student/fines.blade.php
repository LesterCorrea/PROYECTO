@extends('layouts.app')

@section('title', 'Mis Multas')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Mis multas</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Historial de penalizaciones por devoluciones tardías
        </p>
    </div>

    {{-- Resumen --}}
    @php
    $pendingTotal = $fines->where('status', 'pending')->sum('total_amount');
    $paidTotal = $fines->where('status', 'paid')->sum('total_amount');
    @endphp

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
            <p class="text-xs text-red-600 dark:text-red-400 font-medium mb-1">Pendiente por pagar</p>
            <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                ${{ number_format($pendingTotal, 2) }}
            </p>
        </div>
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-4">
            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mb-1">Total pagado</p>
            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">
                ${{ number_format($paidTotal, 2) }}
            </p>
        </div>
    </div>

    {{-- Lista de multas --}}
    <div class="space-y-3">
        @forelse($fines as $fine)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $fine->status === 'pending' ? 'border-red-200 dark:border-red-900/50' : 'border-gray-200 dark:border-gray-700' }} p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $fine->loan->book?->title ?? $fine->loan->magazine?->title ?? '—' }}
                        </p>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0
                                {{ $fine->status === 'paid'
                                    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                    : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ $fine->status === 'paid' ? 'Pagada' : 'Pendiente' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $fine->overdue_days }} días de retraso
                        </span>
                        <span>${{ number_format($fine->amount_per_day, 2) }}/día</span>
                        <span>Generada: {{ $fine->created_at->format('d/m/Y') }}</span>
                        @if($fine->paid_at)
                        <span class="text-emerald-600 dark:text-emerald-400">
                            Pagada: {{ $fine->paid_at->format('d/m/Y') }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
                    <p class="text-xl font-bold {{ $fine->status === 'pending' ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                        ${{ number_format($fine->total_amount, 2) }}
                    </p>
                    @if($fine->status === 'pending')
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Acércate a biblioteca para pagar
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No tienes multas registradas</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                Devuelve los libros a tiempo para evitar penalizaciones.
            </p>
        </div>
        @endforelse
    </div>
</div>
@endsection