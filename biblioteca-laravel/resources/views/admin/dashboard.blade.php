@extends('layouts.panel')

@section('title', 'Dashboard Admin')
@section('page-title', 'Panel de Administración')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
        ['label' => 'Usuarios totales', 'value' => $stats['total_users'], 'color' => 'indigo'],
        ['label' => 'Préstamos activos', 'value' => $stats['active_loans'], 'color' => 'blue'],
        ['label' => 'Multas pendientes', 'value' => $stats['pending_fines'], 'color' => 'red'],
        ['label' => 'Total recaudado', 'value' => '$'.number_format($stats['total_fines_amount'],2), 'color' => 'emerald'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">{{ $card['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Libros más prestados --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Libros más prestados</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($topBooks as $index => $book)
                <div class="flex items-center gap-4 px-6 py-3">
                    <span class="w-6 text-center text-sm font-bold text-gray-400">{{ $index + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $book['title'] ?? $book->title }}
                        </p>
                    </div>
                    <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                        {{ $book['loan_count'] ?? $book->loan_count }} préstamos
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Usuarios más activos --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Usuarios más activos</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($topUsers as $index => $user)
                <div class="flex items-center gap-4 px-6 py-3">
                    <span class="w-6 text-center text-sm font-bold text-gray-400">{{ $index + 1 }}</span>
                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                    <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                        {{ $user->loans_count }} préstamos
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Préstamos vencidos --}}
    @if($overdueLoans->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-900/50">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-red-200 dark:border-red-900/50">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h2 class="font-semibold text-gray-900 dark:text-gray-100">
                Préstamos vencidos sin devolver
            </h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($overdueLoans as $loan)
            <div class="flex items-center justify-between px-6 py-3 gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ $loan->book->title }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $loan->user->name }} · Venció {{ $loan->due_date->format('d/m/Y') }}
                    </p>
                </div>
                <span class="text-sm font-bold text-red-500 flex-shrink-0">
                    {{ now()->diffInDays($loan->due_date) }}d
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection