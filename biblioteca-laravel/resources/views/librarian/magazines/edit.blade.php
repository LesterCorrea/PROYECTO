@extends('layouts.panel')

@section('title', 'Editar Revista')
@section('page-title', 'Editar revista')

@section('content')
<div class="max-w-3xl"
    x-data="bookForm({
         authorRoute:    '{{ route('librarian.quick.author') }}',
         categoryRoute:  '{{ route('librarian.quick.category') }}',
         publisherRoute: '{{ route('librarian.quick.publisher') }}',
         csrf:           '{{ csrf_token() }}'
     })">

    {{-- Modal Autor --}}
    <template x-teleport="body">
        <div x-show="modal === 'author'" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @keydown.escape.window="closeModal()">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 w-full max-w-sm mx-4 shadow-2xl" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Nuevo autor</h3>
                    <button @click="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" placeholder="Nombre completo"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nacionalidad</label>
                        <input type="text" x-model="form.nationality" placeholder="Ej: Colombiano"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <p x-show="error" x-text="error" class="text-xs text-red-500"></p>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Cancelar</button>
                        <button type="button" @click="submitAuthor()" :disabled="loading"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors disabled:opacity-60">
                            <span x-show="!loading">Crear autor</span><span x-show="loading">Guardando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal Categoría --}}
    <template x-teleport="body">
        <div x-show="modal === 'category'" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @keydown.escape.window="closeModal()">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 w-full max-w-sm mx-4 shadow-2xl" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Nueva categoría</h3>
                    <button @click="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" placeholder="Ej: Ciencia"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="form.color"
                                class="w-12 h-10 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" />
                            <span class="text-sm font-mono text-gray-600 dark:text-gray-400" x-text="form.color || '#6366F1'"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aplica a</label>
                        <select x-model="form.type"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="book">Solo libros</option>
                            <option value="magazine" selected>Solo revistas</option>
                            <option value="both">Ambos</option>
                        </select>
                    </div>
                    <p x-show="error" x-text="error" class="text-xs text-red-500"></p>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Cancelar</button>
                        <button type="button" @click="submit('category', 'category_id')" :disabled="loading"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors disabled:opacity-60">
                            <span x-show="!loading">Crear categoría</span><span x-show="loading">Guardando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal Editorial --}}
    <template x-teleport="body">
        <div x-show="modal === 'publisher'" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @keydown.escape.window="closeModal()">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 w-full max-w-sm mx-4 shadow-2xl" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Nueva editorial</h3>
                    <button @click="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" placeholder="Ej: Penguin Books"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">País</label>
                        <input type="text" x-model="form.country" placeholder="Ej: España"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <p x-show="error" x-text="error" class="text-xs text-red-500"></p>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Cancelar</button>
                        <button type="button" @click="submit('publisher', 'publisher_id')" :disabled="loading"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors disabled:opacity-60">
                            <span x-show="!loading">Crear editorial</span><span x-show="loading">Guardando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══ FORMULARIO PRINCIPAL ═══ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST"
            action="{{ route('librarian.revistas.update', $revista) }}"
            enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Título e ISSN --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $revista->title) }}" required
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ISSN</label>
                    <input type="text" name="issn" value="{{ old('issn', $revista->issn) }}"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    @error('issn')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description', $revista->description) }}</textarea>
            </div>

            {{-- Autores actuales con edición inline --}}
            <div x-data="{
                authors: {{ json_encode($revista->authors->map(fn($a) => ['id' => (string)$a->id, 'role' => $a->pivot->role])->values()) }}
            }">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Autores <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="$dispatch('open-author-modal')"
                            class="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo autor
                        </button>
                        <button type="button" @click="authors.push({ id: '', role: 'Autor' })"
                            class="text-xs text-gray-500 dark:text-gray-400 hover:underline">
                            + Añadir fila
                        </button>
                    </div>
                </div>
                <div class="space-y-2" @author-created.window="
                    const empty = authors.find(a => !a.id);
                    if (empty) empty.id = String($event.detail.id);
                    else authors.push({ id: String($event.detail.id), role: 'Autor' });
                ">
                    <template x-for="(author, index) in authors" :key="index">
                        <div class="flex gap-2 items-center">
                            <select :name="`author_ids[${index}]`" required
                                x-model="author.id"
                                class="flex-1 px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Seleccionar autor...</option>
                                @foreach($authors as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endforeach
                            </select>
                            <select :name="`author_roles[${index}]`"
                                x-model="author.role"
                                class="w-36 px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="Autor">Autor</option>
                                <option value="Editor">Editor</option>
                                <option value="Colaborador">Colaborador</option>
                            </select>
                            <button type="button"
                                @click="if(authors.length > 1) authors.splice(index, 1)"
                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Categoría y Editorial --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <button type="button" @click="openModal('category')"
                            class="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nueva
                        </button>
                    </div>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Seleccionar...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $revista->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Editorial</label>
                        <button type="button" @click="openModal('publisher')"
                            class="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nueva
                        </button>
                    </div>
                    <select name="publisher_id" id="publisher_id"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sin editorial</option>
                        @foreach($publishers as $pub)
                        <option value="{{ $pub->id }}"
                            {{ old('publisher_id', $revista->publisher_id) == $pub->id ? 'selected' : '' }}>
                            {{ $pub->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Volumen, Número, Fecha, Idioma --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Número de copias <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="total_copies"
                        value="{{ old('total_copies', $revista->total_copies) }}" min="1" required
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Volumen</label>
                    <input type="number" name="volume"
                        value="{{ old('volume', $revista->volume) }}" min="1"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número</label>
                    <input type="number" name="issue_number"
                        value="{{ old('issue_number', $revista->issue_number) }}" min="1"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha publicación</label>
                    <input type="date" name="published_date"
                        value="{{ old('published_date', $revista->published_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Idioma</label>
                    <input type="text" name="language"
                        value="{{ old('language', $revista->language) }}"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>

            {{-- ═══ PORTADA con tres estados ═══ --}}
            <div x-data="{ preview: null, deleteCover: false }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Portada <span class="text-gray-400 text-xs">(opcional)</span>
                </label>

                {{-- Estado A: portada actual visible --}}
                @if($revista->cover_image)
                <div x-show="!deleteCover && !preview"
                    class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700 mb-3">
                    <img src="{{ $revista->cover_url }}"
                        alt="Portada actual"
                        class="w-12 aspect-[2/3] object-cover rounded-lg shadow-sm flex-shrink-0" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Portada actual</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            Selecciona un archivo para reemplazarla o elimínala
                        </p>
                    </div>
                    <button type="button" @click="deleteCover = true"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar portada
                    </button>
                </div>
                @endif

                {{-- Estado B: marcado para eliminar --}}
                <div x-show="deleteCover && !preview"
                    class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl mb-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm text-red-700 dark:text-red-400 flex-1">
                        La portada se eliminará al guardar.
                    </p>
                    <button type="button" @click="deleteCover = false"
                        class="text-xs font-medium text-red-600 dark:text-red-400 hover:underline flex-shrink-0">
                        Deshacer
                    </button>
                    <input type="hidden" name="delete_cover" value="1" />
                </div>

                {{-- Estado C: preview de nueva imagen --}}
                <div x-show="preview"
                    class="flex items-center gap-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl mb-3">
                    <img :src="preview" class="w-12 aspect-[2/3] object-cover rounded-lg shadow-sm flex-shrink-0" />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Nueva portada seleccionada</p>
                        <p class="text-xs text-indigo-500 dark:text-indigo-400 mt-0.5">Se guardará al confirmar</p>
                    </div>
                    <button type="button"
                        @click="preview = null; $refs.coverInput.value = ''; deleteCover = false"
                        class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex-shrink-0">
                        Quitar
                    </button>
                </div>

                {{-- Input archivo --}}
                <div x-show="!deleteCover">
                    <input type="file" name="cover_image" accept="image/*"
                        x-ref="coverInput"
                        @change="
                               preview = $event.target.files[0]
                                   ? URL.createObjectURL($event.target.files[0])
                                   : null;
                               deleteCover = false;
                           "
                        class="w-full text-sm text-gray-600 dark:text-gray-400
                                  file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                                  dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                                  hover:file:bg-indigo-100 cursor-pointer" />
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        @if($revista->cover_image)
                        Selecciona una imagen para reemplazar la portada actual.
                        @else
                        Sin portada. Puedes subir una imagen opcional.
                        @endif
                    </p>
                </div>

                @error('cover_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- PDF --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nuevo PDF <span class="text-gray-400 text-xs">(opcional — deja vacío para mantener el actual)</span>
                </label>
                <input type="file" name="pdf_file" accept=".pdf"
                    class="w-full text-sm text-gray-600 dark:text-gray-400
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-medium file:bg-red-50 file:text-red-700
                              dark:file:bg-red-900/30 dark:file:text-red-400
                              hover:file:bg-red-100 cursor-pointer" />
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Máximo 50MB</p>
                @error('pdf_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('librarian.revistas.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bookForm(config) {
        return {
            modal: null,
            form: {
                color: '#6366F1',
                type: 'magazine'
            },
            loading: false,
            error: null,
            routes: {
                author: config.authorRoute,
                category: config.categoryRoute,
                publisher: config.publisherRoute,
            },
            csrf: config.csrf,

            openModal(type) {
                this.modal = type;
                this.form = {
                    color: '#6366F1',
                    type: 'magazine'
                };
                this.error = null;
                this.loading = false;
            },

            closeModal() {
                this.modal = null;
            },

            async submitAuthor() {
                if (!this.form.name?.trim()) {
                    this.error = 'El nombre es obligatorio.';
                    return;
                }
                this.loading = true;
                this.error = null;
                try {
                    const response = await fetch(this.routes.author, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        this.error = Object.values(data.errors || {}).flat().join('. ') || 'Error al crear.';
                        return;
                    }

                    // Añadir al DOM en todos los selects de autores existentes
                    document.querySelectorAll('[name^="author_ids"]').forEach(select => {
                        const opt = document.createElement('option');
                        opt.value = data.id;
                        opt.text = data.name;
                        select.add(opt);
                    });

                    // Emitir evento para asignación automática
                    window.dispatchEvent(new CustomEvent('author-created', {
                        detail: {
                            id: String(data.id),
                            name: data.name
                        }
                    }));

                    this.closeModal();
                } catch (e) {
                    this.error = 'Error de conexión.';
                } finally {
                    this.loading = false;
                }
            },

            async submit(type, selectName) {
                if (!this.form.name?.trim()) {
                    this.error = 'El nombre es obligatorio.';
                    return;
                }
                this.loading = true;
                this.error = null;
                try {
                    const response = await fetch(this.routes[type], {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        this.error = Object.values(data.errors || {}).flat().join('. ') || 'Error al crear.';
                        return;
                    }
                    const select = document.getElementById(selectName);
                    if (select) {
                        const opt = document.createElement('option');
                        opt.value = data.id;
                        opt.text = data.name;
                        opt.selected = true;
                        select.add(opt);
                        select.value = data.id;
                    }
                    this.closeModal();
                } catch (e) {
                    this.error = 'Error de conexión.';
                } finally {
                    this.loading = false;
                }
            }
        };
    }
</script>
@endpush