@extends('layouts.app')

@section('title', 'Mi Historial')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <a href="{{ route('student.dashboard') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 transition-colors bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">

            {{-- Icono de flecha hacia la izquierda --}}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>

            Volver
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Historial de préstamos</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ count($history) }} préstamo(s) en total
        </p>
    </div>

    {{-- Línea de tiempo --}}
    <div class="relative">
        <div class="absolute left-6 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>

        <div class="space-y-4">
            @forelse($history as $loan)
            <div class="relative flex gap-5">
                {{-- Punto en la línea de tiempo --}}
                <div class="flex-shrink-0 w-12 flex justify-center">
                    <div class="w-3 h-3 rounded-full mt-4 ring-2 ring-white dark:ring-gray-950
                            {{ $loan->status === 'returned' ? 'bg-gray-400 dark:bg-gray-500' : '' }}
                            {{ $loan->status === 'active'   ? 'bg-indigo-500' : '' }}
                            {{ $loan->status === 'overdue'  ? 'bg-red-500' : '' }}">
                    </div>
                </div>

                {{-- Tarjeta --}}
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 mb-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 aspect-[2/3] rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                            <img src="{{ $loan->book?->cover_url ?? $loan->magazine?->cover_url ?? '' }}"
                                alt="Tarjeta"
                                class="w-full h-full object-cover" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $loan->book?->title ?? $loan->magazine?->title ?? '—' }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        @if($loan->book_id)
                                        📖 Libro · {{ $loan->book->author->name ?? 'Sin autor' }}
                                        @elseif($loan->magazine_id)
                                        📰 Revista · {{ $loan->megazine->author->name ?? '' }}
                                        @else
                                        —
                                        @endif
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full flex-shrink-0
                                        {{ $loan->status === 'returned' ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}
                                        {{ $loan->status === 'active'   ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                                        {{ $loan->status === 'overdue'  ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}">
                                    {{ match($loan->status) {
                                            'returned' => 'Devuelto',
                                            'active'   => 'Activo',
                                            'overdue'  => 'Vencido',
                                            default    => $loan->status,
                                        } }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>Prestado: {{ $loan->loan_date->format('d/m/Y') }}</span>
                                <span>Debía devolver: {{ $loan->due_date->format('d/m/Y') }}</span>
                                @if($loan->return_date)
                                <span>Devuelto: {{ $loan->return_date->format('d/m/Y') }}</span>
                                @endif
                            </div>

                            @if($loan->fine)
                            <div class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium text-red-600 dark:text-red-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Multa: ${{ number_format($loan->fine->total_amount, 2) }}
                                ({{ $loan->fine->status === 'paid' ? 'Pagada' : 'Pendiente' }})
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Sin historial de préstamos todavía</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection