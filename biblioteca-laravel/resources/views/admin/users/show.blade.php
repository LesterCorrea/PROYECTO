@extends('layouts.panel')

@section('title', $usuario->name)
@section('page-title', 'Detalle de usuario')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.usuarios.index') }}"
            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $usuario->name }}</h2>
        <a href="{{ route('admin.usuarios.edit', $usuario) }}"
            class="ml-auto flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Info principal --}}
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                    {{ strtoupper(substr($usuario->name, 0, 1)) }}
                </div>
                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $usuario->name }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $usuario->email }}</p>
                @foreach($usuario->roles as $role)
                <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full
                        {{ $role->name === 'admin'     ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}
                        {{ $role->name === 'librarian' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                        {{ $role->name === 'student'   ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}">
                    {{ ucfirst($role->name) }}
                </span>
                @endforeach
                <div class="mt-4">
                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        {{ $usuario->is_active
                            ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                        {{ $usuario->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Detalles --}}
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    Información
                </h3>
                <dl class="space-y-2">
                    @foreach([
                    ['Carnet', $usuario->student_id ?? '—'],
                    ['Teléfono', $usuario->phone ?? '—'],
                    ['Registro', $usuario->created_at->format('d/m/Y')],
                    ] as [$label, $value])
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            {{-- Estadísticas --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $usuario->loans->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Préstamos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $usuario->reservations->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reservas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $usuario->fines->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Multas</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de préstamos --}}
    @if($usuario->loans->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Historial de préstamos</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($usuario->loans->take(10) as $loan)
            <div class="flex items-center justify-between px-6 py-3 gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ $loan->book?->title ?? $loan->magazine?->title ?? '—' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $loan->loan_date->format('d/m/Y') }} → {{ $loan->due_date->format('d/m/Y') }}
                    </p>
                </div>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full flex-shrink-0
                            {{ $loan->status === 'returned' ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}
                            {{ $loan->status === 'active'   ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                            {{ $loan->status === 'overdue'  ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}">
                    {{ match($loan->status) { 'returned'=>'Devuelto','active'=>'Activo','overdue'=>'Vencido',default=>$loan->status } }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection