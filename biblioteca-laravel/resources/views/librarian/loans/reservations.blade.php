@extends('layouts.panel')

@section('title', 'Reservas')
@section('page-title', 'Gestión de Reservas')

@section('content')
<div class="space-y-4">
    {{-- Filtro de estado --}}
    <div class="flex gap-2">
        @foreach(['pending' => 'Pendientes', 'confirmed' => 'Confirmadas', 'rejected' => 'Rechazadas', 'completed' => 'Completadas'] as $key => $label)
        <a href="{{ route('librarian.reservations', ['status' => $key]) }}"
            class="px-4 py-2 text-sm font-medium rounded-xl transition-colors
                      {{ $status === $key
                          ? 'bg-indigo-600 text-white'
                          : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">#Cola</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Libro</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Estudiante</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Fecha</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Vence</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($reservations as $reservation)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold">
                            {{ $reservation->queue_position }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 min-w-0">
                                {{-- 1. Título dinámico: Libro o Revista --}}
                                <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $reservation->book?->title ?? $reservation->magazine?->title ?? '—' }}
                                </p>
                                
                                {{-- 2. Subtítulo dinámico: Autor del libro o indicación de revista --}}
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($reservation->book_id)
                                        📖 Libro · {{ $reservation->book->author->name ?? 'Sin autor' }}
                                    @elseif($reservation->magazine_id)
                                        📰 Revista
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $reservation->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reservation->user->email }}</p>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-gray-600 dark:text-gray-400">
                        {{ $reservation->reserved_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        @if($reservation->expires_at)
                        <span class="{{ $reservation->expires_at->isPast() ? 'text-red-500' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $reservation->expires_at->format('d/m/Y') }}
                        </span>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            @if($reservation->status === 'pending')
                            <form method="POST"
                                action="{{ route('librarian.reservations.confirm', $reservation) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    Confirmar
                                </button>
                            </form>
                            <form method="POST"
                                action="{{ route('librarian.reservations.reject', $reservation) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors">
                                    Rechazar
                                </button>
                            </form>

                            {{-- CONFIRMADA: mostrar botón para convertir en préstamo --}}
                            @elseif($reservation->status === 'confirmed')
                            <!-- <button onclick="document.getElementById('modal-loan-{{ $reservation->id }}').classList.remove('hidden')"
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors">
                                Registrar préstamo
                            </button> -->

                            <button onclick="document.getElementById('modal-loan-{{ $reservation->id }}').classList.replace('hidden', 'flex')"
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors">
                                Registrar préstamo
                            </button>

                            {{-- Modal inline para esta reserva --}}
                            <div id="modal-loan-{{ $reservation->id }}"
                                class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 backdrop-blur-sm">
                                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 w-full max-w-md mx-4 shadow-2xl">
                                    <div class="flex items-center justify-between mb-5">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Registrar préstamo</h3>
                                        <button onclick="document.getElementById('modal-loan-{{ $reservation->id }}').classList.replace('flex', 'hidden')"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Info de la reserva --}}
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mb-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-14 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                                <img src="{{ $reservation->book?->cover_url ?? $reservation->magazine?->cover_url ?? asset('images/default-cover.png') }}"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $reservation->book?->title ?? $reservation->magazine?->title }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                    Estudiante: {{ $reservation->user->name }}
                                                </p>
                                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">
                                                    Reserva confirmada — el estudiante ha venido a recogerlo
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('librarian.loans.store') }}" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $reservation->user_id }}" />
                                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}" />
                                        @if($reservation->book_id)
                                        <input type="hidden" name="book_id" value="{{ $reservation->book_id }}" />
                                        @endif

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Fecha de devolución <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" name="due_date" required
                                                min="{{ now()->addDay()->toDateString() }}"
                                                value="{{ now()->addDays(14)->toDateString() }}"
                                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                            <p class="text-xs text-gray-400 mt-1">Por defecto 14 días</p>
                                        </div>

                                        <div class="flex justify-end gap-3 pt-2">
                                            <button type="button"
                                                onclick="document.getElementById('modal-loan-{{ $reservation->id }}').classList.replace('flex', 'hidden')"
                                                class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                                                Cancelar
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                                Confirmar préstamo
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @else
                                <span class="px-3 py-1.5 text-xs font-medium rounded-lg
                                    {{ $reservation->status === 'completed' ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}
                                    {{ $reservation->status === 'rejected'  ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}
                                    {{ $reservation->status === 'cancelled' ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' : '' }}">
                                    {{ match($reservation->status) {
                                        'completed' => 'Completada',
                                        'rejected'  => 'Rechazada',
                                        'cancelled' => 'Cancelada',
                                        default     => ucfirst($reservation->status),
                                    } }}
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        No hay reservas con este estado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($reservations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $reservations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection