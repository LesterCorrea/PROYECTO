@extends('layouts.panel')

@section('title', 'Logs de Actividad')
@section('page-title', 'Logs de Actividad')

@section('content')
<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="q" value="{{ $query }}"
                placeholder="Buscar en descripción..."
                class="flex-1 min-w-48 px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <select name="evento"
                class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos los eventos</option>
                @foreach($events as $e)
                <option value="{{ $e }}" {{ $event === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
            <input type="date" name="desde" value="{{ $from }}"
                class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <input type="date" name="hasta" value="{{ $to }}"
                class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                Filtrar
            </button>
        </form>
    </div>

    {{-- Lista de logs --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($logs as $log)
            <div class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        <span class="font-medium">{{ $log->causer->name ?? 'Sistema' }}</span>
                        <span class="text-gray-500 dark:text-gray-400"> — {{ $log->description }}</span>
                    </p>
                    @if($log->properties && $log->properties->isNotEmpty())
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-mono">
                        {{ json_encode($log->properties->get('attributes', []), JSON_PRETTY_PRINT) }}
                    </p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                        {{ $log->event ?? 'log' }}
                    </span>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                No hay registros de actividad para este filtro.
            </div>
            @endforelse
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection