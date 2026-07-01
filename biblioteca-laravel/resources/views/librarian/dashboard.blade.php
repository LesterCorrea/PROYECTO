@extends('layouts.panel')

@section('title', 'Dashboard Bibliotecario')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @php
        $statCards = [
        ['label' => 'Reservas pendientes', 'value' => $stats['pending_reservations'], 'color' => 'yellow', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Préstamos activos', 'value' => $stats['active_loans'], 'color' => 'blue', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13'],
        ['label' => 'Préstamos vencidos', 'value' => $stats['overdue_loans'], 'color' => 'red', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['label' => 'Multas pendientes', 'value' => $stats['pending_fines'], 'color' => 'orange', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        ['label' => 'Total libros', 'value' => $stats['total_books'], 'color' => 'indigo', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13'],
        ['label' => 'Total revistas', 'value' => $stats['total_magazines'], 'color' => 'purple', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7'],
        ];
        @endphp

        @foreach($statCards as $card)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Reservas pendientes --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Reservas pendientes</h2>
                <a href="{{ route('librarian.reservations') }}"
                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                    Ver todas
                </a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($pendingReservations as $reservation)
                <div class="flex items-center justify-between px-6 py-3 gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $reservation->book->title }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $reservation->user->name }} · #{{ $reservation->queue_position }} en cola
                        </p>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form method="POST"
                            action="{{ route('librarian.reservations.confirm', $reservation) }}">
                            @csrf @method('PATCH')
                            <button class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                                Confirmar
                            </button>
                        </form>
                        <form method="POST"
                            action="{{ route('librarian.reservations.reject', $reservation) }}">
                            @csrf @method('PATCH')
                            <button class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors">
                                Rechazar
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 px-6 py-8 text-center">
                    No hay reservas pendientes 🎉
                </p>
                @endforelse
            </div>
        </div>

        {{-- Préstamos vencidos --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Préstamos vencidos</h2>
                <a href="{{ route('librarian.loans.index', ['status' => 'overdue']) }}"
                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                    Ver todos
                </a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($overdueLoans as $loan)
                <div class="flex items-center justify-between px-6 py-3 gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $loan->book->title }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $loan->user->name }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-xs font-semibold text-red-500">
                            {{ now()->diffInDays($loan->due_date) }} días
                        </span>
                        <p class="text-xs text-gray-400">vencido</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 px-6 py-8 text-center">
                    No hay préstamos vencidos 🎉
                </p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection