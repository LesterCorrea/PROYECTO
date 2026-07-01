@extends('layouts.app')

@section('title', 'Mi Cuenta')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Cabecera --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Bienvenido, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ auth()->user()->student_id ? 'Carnet: ' . auth()->user()->student_id : 'Estudiante' }}
            </p>
        </div>
        <a href="{{ route('books.index') }}"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
            </svg>
            Explorar catálogo
        </a>
    </div>

    {{-- Alerta de multas pendientes --}}
    @if(auth()->user()->hasPendingFines())
    <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 mb-6">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">
                Tienes multas pendientes
            </p>
            <p class="text-xs text-red-600 dark:text-red-500 mt-0.5">
                No puedes realizar nuevas reservas hasta que las pagues.
            </p>
            <a href="{{ route('student.fines') }}"
                class="inline-block mt-2 text-xs font-medium text-red-700 dark:text-red-400 underline">
                Ver mis multas
            </a>
        </div>
    </div>
    @endif


    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 mb-6">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" />
            </svg>
            <h2 class="font-semibold text-gray-900 dark:text-gray-100">Préstamos</h2>
        </div>
        {{-- Préstamos activos --}}
        <div class="grid grid-cols-1 gap-4 mb-6">
            @forelse($activeLoans as $loan)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center gap-4">
                    {{-- Usamos el accessor 'item' definido en tu modelo Loan --}}
                    <div class="w-14 aspect-[2/3] rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                        <img src="{{ $loan->item->cover_url ?? '' }}"
                            alt="{{ $loan->item_title }}"
                            class="w-full h-full object-cover" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $loan->item_title }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Fecha préstamo: {{ $loan->loan_date->format('d/m/Y') }}
                        </p>
                    </div>

                    @if($loan->book_id)
                    <a href="{{ route('books.show', $loan->book->isbn) }}" class="text-indigo-600 text-sm">Ver libro</a>
                    @elseif($loan->magazine_id)
                    <a href="{{ route('magazines.show', $loan->magazine->id) }}" class="text-indigo-600 text-sm">Ver revista</a>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-500">No tienes préstamos activos.</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Reservas en cola FIFO --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Mis reservas</h2>
                </div>
                <a href="{{ route('student.reservations.index') }}"
                    class="text-xs text-indigo-600 dark:text-indigo-400">
                    Ver todas
                </a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($pendingReservations->take(4) as $reservation)
                <div class="flex items-center gap-3 px-6 py-3">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex-shrink-0">
                        {{ $reservation->queue_position }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $reservation->book?->title ?? $reservation->magazine?->title ?? '—' }}
                        </p>
                        <p class="text-xs mt-0.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $reservation->status === 'confirmed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' }}">
                                {{ $reservation->status === 'confirmed' ? 'Confirmada' : 'Pendiente' }}
                            </span>
                        </p>
                    </div>
                    <form method="POST"
                        action="{{ route('student.reservations.cancel', $reservation) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Cancelar esta reserva?')"
                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-500 mb-3">
                        No tienes reservas activas
                    </p>
                    <a href="{{ route('books.index') }}"
                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        Explorar catálogo
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Historial reciente (Lista enlazada) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Historial reciente</h2>
                </div>
                <a href="{{ route('student.history') }}"
                    class="text-xs text-indigo-600 dark:text-indigo-400">
                    Ver todo
                </a>
            </div>
            {{-- Historial reciente --}}
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentLoans as $loan)
                <div class="flex items-center gap-3 px-6 py-3">
                    <div class="w-8 h-11 rounded bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                        <img src="{{ $loan->item->cover_url ?? '' }}"
                            alt="{{ $loan->item_title }}"
                            class="w-full h-full object-cover" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $loan->item_title }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $loan->loan_date->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0
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
                @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        Sin historial de préstamos
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection