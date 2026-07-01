@extends('layouts.panel')

@section('title', 'Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="space-y-4">

    {{-- Cabecera --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="q" value="{{ $query }}"
                placeholder="Buscar usuario..."
                class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <select name="rol"
                class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos los roles</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('rol') === $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
                @endforeach
            </select>
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                Filtrar
            </button>
        </form>

        <a href="{{ route('admin.usuarios.create') }}"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Crear usuario
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Usuario</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Rol</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden lg:table-cell">Registro</th>
                    <th class="text-center px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Estado</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($user['name'] ?? '', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $user['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user['email'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        @foreach($user['roles'] ?? [] as $role)
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $role['name'] === 'admin'     ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}
                                    {{ $role['name'] === 'librarian' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                                    {{ $role['name'] === 'student'   ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}">
                            {{ ucfirst($role['name']) }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell text-gray-500 dark:text-gray-400 text-xs">
                        {{ isset($user['created_at']) ? \Carbon\Carbon::parse($user['created_at'])->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php $userModel = \App\Models\User::find($user['id']); @endphp
                        <form method="POST"
                            action="{{ route('admin.usuarios.toggle', $user['id']) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-2.5 py-1 text-xs font-medium rounded-full transition-colors
                                               {{ $user['is_active']
                                                   ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200'
                                                   : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200' }}">
                                {{ $user['is_active'] ? 'Activo' : 'Inactivo' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.usuarios.show', $user['id']) }}"
                                class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.usuarios.edit', $user['id']) }}"
                                class="p-1.5 text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @if($user['id'] !== auth()->id())
                            <form method="POST"
                                action="{{ route('admin.usuarios.destroy', $user['id']) }}"
                                onsubmit="return confirm('Eliminar este usuario?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        No se encontraron usuarios.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection