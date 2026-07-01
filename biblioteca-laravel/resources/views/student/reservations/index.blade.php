@extends('layouts.app')

@section('title', 'Mis Reservas')

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

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Mis reservas</h1>
        <a href="{{ route('books.index') }}"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva reserva
        </a>
    </div>

    {{-- Posición en cola --}}
    @if($queue->size() > 0)
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-2xl p-4 mb-6">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-indigo-700 dark:text-indigo-300">
                Tienes <strong>{{ $queue->size() }}</strong> reserva(s) activa(s) en cola.
                El sistema gestiona las reservas en orden FIFO — primero en llegar, primero en ser atendido.
            </p>
        </div>
    </div>
    @endif

    {{-- Lista de reservas --}}
    <div class="space-y-3">
        @forelse($reservations as $reservation)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start gap-4">

                {{-- Portada --}}
                <div class="w-16 aspect-[2/3] rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                    <img src="{{ $reservation->book?->cover_url ?? $reservation->magazine?->cover_url ?? '' }}"
                        alt="Portada"
                        class="w-full h-full object-cover" />
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $reservation->book?->title ?? $reservation->magazine?->title ?? '—' }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                @if($reservation->book_id)
                                📖 Libro · {{ $reservation->book->author->name ?? 'Sin autor' }}
                                @elseif($reservation->magazine_id)
                                📰 Revista · {{ $reservation->megazine->author->name ?? '' }}
                                @else
                                —
                                @endif
                            </p>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full flex-shrink-0
                                {{ $reservation->status === 'confirmed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : '' }}
                                {{ $reservation->status === 'pending'   ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : '' }}
                                {{ $reservation->status === 'rejected'  ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}
                                {{ $reservation->status === 'cancelled' ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' : '' }}
                                {{ $reservation->status === 'completed' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}">
                            {{ match($reservation->status) {
                                    'confirmed' => 'Confirmada',
                                    'pending'   => 'Pendiente',
                                    'rejected'  => 'Rechazada',
                                    'cancelled' => 'Cancelada',
                                    'completed' => 'Completada',
                                    default     => $reservation->status,
                                } }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 mt-3">
                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Posición en cola: <strong class="text-indigo-600 dark:text-indigo-400">#{{ $reservation->queue_position }}</strong>
                        </div>
                        @endif

                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Reservado: {{ $reservation->reserved_at->format('d/m/Y') }}
                        </div>

                        @if($reservation->expires_at && $reservation->status === 'confirmed')
                        <div class="flex items-center gap-1.5 text-xs {{ $reservation->expires_at->isPast() ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Recoger antes del: {{ $reservation->expires_at->format('d/m/Y') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            @if($reservation->status === 'pending')
            <div class="flex justify-end mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <form method="POST"
                    action="{{ route('student.reservations.cancel', $reservation) }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Cancelar esta reserva?')"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar reserva
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 mb-4">No tienes reservas realizadas</p>
            <a href="{{ route('books.index') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                Explorar catálogo
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection