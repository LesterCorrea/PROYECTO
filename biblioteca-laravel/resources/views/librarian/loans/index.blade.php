@extends('layouts.panel')

@section('title', 'Préstamos')
@section('page-title', 'Gestión de Préstamos')

@section('content')
<div class="space-y-4">

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-2 items-center justify-between">
        <div class="flex gap-2">
            @foreach(['active' => 'Activos', 'overdue' => 'Vencidos', 'returned' => 'Devueltos'] as $key => $label)
            <a href="{{ route('librarian.loans.index', ['status' => $key]) }}"
                class="px-4 py-2 text-sm font-medium rounded-xl transition-colors
                          {{ $status === $key
                              ? 'bg-indigo-600 text-white'
                              : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        {{-- Registrar préstamo manual --}}
        <button onclick="document.getElementById('modal-loan').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Registrar préstamo
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Libro</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Estudiante</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Préstamo</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Vence</th>
                    <th class="text-right px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($loans as $loan)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors
                            {{ $loan->isOverdue() ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate max-w-xs">
                            {{-- Usamos el accessor que creaste en tu modelo --}}
                            {{ $loan->item_title }} 
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @if($loan->book_id)
                                📖 Libro · {{ $loan->book->author->name ?? 'Sin autor' }}
                            @elseif($loan->magazine_id)
                                📰 Revista 
                                {{-- Si tu modelo Magazine tiene volumen o número, podrías ponerlo aquí --}}
                            @else
                                —
                            @endif
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $loan->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $loan->user->email }}</p>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-gray-600 dark:text-gray-400">
                        {{ $loan->loan_date->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <span class="{{ $loan->isOverdue() ? 'text-red-500 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $loan->due_date->format('d/m/Y') }}
                        </span>
                        @if($loan->isOverdue())
                        <p class="text-xs text-red-500">
                            {{ $loan->overdueDays() }} días de retraso
                        </p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end">
                            @if($loan->status !== 'returned')
                            <form method="POST"
                                action="{{ route('librarian.loans.return', $loan) }}"
                                onsubmit="return confirm('Registrar devolución de este libro?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    Registrar devolución
                                </button>
                            </form>
                            @else
                            <span class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                Devuelto {{ $loan->return_date->format('d/m/Y') }}
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                        No hay préstamos con este estado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($loans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $loans->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal registrar préstamo --}}
<div id="modal-loan"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    x-data="loanModal()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                w-full max-w-lg mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Registrar préstamo</h3>
            <button onclick="document.getElementById('modal-loan').classList.add('hidden')"
                class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('librarian.loans.store') }}" class="p-6 space-y-5">
            @csrf

            {{-- Buscador de estudiante --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Estudiante <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                        x-model="studentQuery"
                        @input.debounce.400ms="searchStudents()"
                        @focus="showStudents = true"
                        placeholder="Buscar por nombre, email o carnet..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                </div>

                {{-- Estudiante seleccionado --}}
                <div x-show="selectedStudent"
                    class="mt-2 flex items-center gap-2 p-2.5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                    <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                        x-text="selectedStudent?.name?.charAt(0)?.toUpperCase()"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-700 dark:text-indigo-300" x-text="selectedStudent?.name"></p>
                        <p class="text-xs text-indigo-500 dark:text-indigo-400" x-text="selectedStudent?.email"></p>
                    </div>
                    <button type="button" @click="clearStudent()"
                        class="text-indigo-400 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <input type="hidden" name="user_id" :value="selectedStudent?.id" />
                </div>

                {{-- Dropdown de estudiantes --}}
                <div x-show="showStudents && students.length > 0 && !selectedStudent"
                    @click.outside="showStudents = false"
                    class="mt-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg max-h-48 overflow-y-auto z-10">
                    <template x-for="student in students" :key="student.id">
                        <button type="button" @click="selectStudent(student)"
                            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left">
                            <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                x-text="student.name.charAt(0).toUpperCase()"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="student.name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="student.email + (student.student_id ? ' · ' + student.student_id : '')"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Buscador de libro/revista --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Libro o Revista <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                        x-model="itemQuery"
                        @input.debounce.400ms="searchItems()"
                        @focus="showItems = true"
                        placeholder="Buscar por título o ISBN..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                </div>

                {{-- Item seleccionado --}}
                <div x-show="selectedItem"
                    class="mt-2 flex items-center gap-3 p-2.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                    <div class="w-8 h-11 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                        <img :src="selectedItem?.cover" class="w-full h-full object-cover" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300 truncate" x-text="selectedItem?.title"></p>
                        <p class="text-xs text-emerald-500 dark:text-emerald-400">
                            <span x-text="selectedItem?.type === 'book' ? 'Libro' : 'Revista'"></span>
                            · <span x-text="selectedItem?.available + ' disponible(s)'"></span>
                        </p>
                    </div>
                    <button type="button" @click="clearItem()"
                        class="text-emerald-400 hover:text-emerald-600 transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <input type="hidden" name="book_id" :value="selectedItem?.type === 'book'     ? selectedItem?.id : ''" />
                    <input type="hidden" name="magazine_id" :value="selectedItem?.type === 'magazine' ? selectedItem?.id : ''" />
                </div>

                {{-- Dropdown de items --}}
                <div x-show="showItems && items.length > 0 && !selectedItem"
                    @click.outside="showItems = false"
                    class="mt-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg max-h-56 overflow-y-auto z-10">
                    <template x-for="item in items" :key="item.type + '_' + item.id">
                        <button type="button" @click="selectItem(item)"
                            :disabled="item.available < 1"
                            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left disabled:opacity-50 disabled:cursor-not-allowed">
                            <div class="w-8 h-11 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                <img :src="item.cover" class="w-full h-full object-cover" loading="lazy" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="item.title"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="item.subtitle"></p>
                                <p class="text-xs"
                                    :class="item.available > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500'"
                                    x-text="item.available > 0 ? item.available + ' disponible(s)' : 'Sin copias'"></p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0"
                                :class="item.type === 'book'
                                      ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                                      : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'"
                                x-text="item.type === 'book' ? 'Libro' : 'Revista'">
                            </span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Fecha de devolución --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Fecha de devolución <span class="text-red-500">*</span>
                </label>
                <input type="date" name="due_date" required
                    min="{{ now()->addDay()->toDateString() }}"
                    value="{{ now()->addDays(14)->toDateString() }}"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="button"
                    onclick="document.getElementById('modal-loan').classList.add('hidden')"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    :disabled="!selectedStudent || !selectedItem"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Registrar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loanModal() {
    return {
        // Estudiante
        studentQuery:    '',
        students:        [],
        selectedStudent: null,
        showStudents:    false,

        // Item (libro o revista)
        itemQuery:    '',
        items:        [],
        selectedItem: null,
        showItems:    false,

        csrf: '{{ csrf_token() }}',

        async searchStudents() {
            if (this.studentQuery.length < 2) { this.students = []; return; }
            const res = await fetch(`/api/buscar-usuarios?q=${encodeURIComponent(this.studentQuery)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf }
            });
            this.students    = await res.json();
            this.showStudents = true;
        },

        selectStudent(student) {
            this.selectedStudent = student;
            this.showStudents    = false;
            this.studentQuery    = student.name;
        },

        clearStudent() {
            this.selectedStudent = null;
            this.studentQuery    = '';
            this.students        = [];
        },

        async searchItems() {
            if (this.itemQuery.length < 2) { this.items = []; return; }
            const res = await fetch(`/api/buscar-items?q=${encodeURIComponent(this.itemQuery)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf }
            });
            this.items    = await res.json();
            this.showItems = true;
        },

        selectItem(item) {
            if (item.available < 1) return;
            this.selectedItem = item;
            this.showItems    = false;
            this.itemQuery    = item.title;
        },

        clearItem() {
            this.selectedItem = null;
            this.itemQuery    = '';
            this.items        = [];
        },
    };
}
</script>
@endpush