<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Leyendo — {{ $book->title }}</title>

    {{-- PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css'])

    <style>
        body { overflow: hidden; }
        #pdf-canvas { display: block; margin: 0 auto; box-shadow: 0 4px 32px rgba(0,0,0,0.5); }
        .page-transition { transition: opacity 0.15s ease; }
        .page-transition.loading { opacity: 0.4; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 3px; }
    </style>
</head>

<body class="bg-gray-950 h-full flex flex-col select-none"
      x-data="pdfReader()"
      @keydown.window="handleKeydown($event)"
      x-init="init()">

    {{-- ═══ BARRA SUPERIOR ═══ --}}
    <header class="flex-shrink-0 bg-gray-900 border-b border-gray-800 px-4 py-3 z-20">
        <div class="flex items-center gap-4">

            {{-- Volver --}}
            <a href="{{ $type === 'libro' ? route('books.show', $book->isbn ?? $book->id) : route('magazines.show', $book->id) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="text-sm hidden sm:block">Volver</span>
            </a>

            {{-- Info del libro --}}
            <div class="flex-1 min-w-0">
                <p class="text-white font-semibold text-sm truncate">{{ $book->title }}</p>
                <p class="text-gray-400 text-xs truncate">
                    @if($type === 'libro')
                        {{ $book->author?->name ?? '' }}
                    @else
                        {{ $book->authors?->pluck('name')->join(', ') ?? '' }}
                    @endif
                </p>
            </div>

            {{-- Progreso --}}
            <div class="hidden sm:flex items-center gap-3 flex-shrink-0">
                <div class="text-right">
                    <p class="text-xs text-gray-400">
                        Pág. <span class="text-white font-semibold" x-text="currentPage"></span>
                        de <span x-text="totalPages"></span>
                    </p>
                    <p class="text-xs text-indigo-400 font-semibold" x-text="percentage + '%'"></p>
                </div>
                <div class="w-24 bg-gray-700 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500"
                         :style="'width:' + percentage + '%'"></div>
                </div>
            </div>

            {{-- Controles --}}
            <div class="flex items-center gap-1 flex-shrink-0">

                {{-- Zoom out --}}
                <button @click="zoomOut()"
                        :disabled="scale <= 0.5"
                        title="Reducir zoom"
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors disabled:opacity-40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0M13 10H7"/>
                    </svg>
                </button>

                {{-- Zoom nivel --}}
                <button @click="cycleZoom()"
                        title="Cambiar zoom"
                        class="px-2 py-1.5 text-xs font-mono text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors min-w-12 text-center">
                    <span x-text="Math.round(scale * 100) + '%'"></span>
                </button>

                {{-- Zoom in --}}
                <button @click="zoomIn()"
                        :disabled="scale >= 3"
                        title="Aumentar zoom"
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors disabled:opacity-40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0M10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </button>

                {{-- Ajustar ancho --}}
                <button @click="fitToWidth()"
                        title="Ajustar al ancho"
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>

                {{-- Pantalla completa --}}
                <button @click="toggleFullscreen()"
                        title="Pantalla completa"
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                    <svg x-show="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <svg x-show="isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                    </svg>
                </button>

                {{-- Indicador guardado --}}
                <div x-show="saved" x-transition
                     class="flex items-center gap-1.5 px-2 py-1 text-xs text-emerald-400 bg-emerald-900/30 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="hidden sm:block">Guardado</span>
                </div>
            </div>
        </div>

        {{-- Barra de progreso delgada --}}
        <div class="mt-2 w-full bg-gray-800 rounded-full h-0.5 overflow-hidden">
            <div class="bg-indigo-500 h-0.5 rounded-full transition-all duration-700"
                 :style="'width:' + percentage + '%'"></div>
        </div>
    </header>

    {{-- ═══ ÁREA DEL PDF ═══ --}}
    <main id="pdf-container"
          class="flex-1 overflow-y-auto overflow-x-auto bg-gray-950 py-6"
          style="scroll-behavior: smooth;">

        {{-- Estado de carga --}}
        <div x-show="loading && !error"
             class="flex flex-col items-center justify-center h-full gap-4">
            <div class="w-10 h-10 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-gray-400 text-sm" x-text="loadingText"></p>
        </div>

        {{-- Error --}}
        <div x-show="error"
             class="flex flex-col items-center justify-center h-full gap-4 px-6 text-center">
            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-white font-semibold mb-1">No se pudo cargar el PDF</p>
                <p class="text-gray-400 text-sm" x-text="error"></p>
            </div>
            <button @click="init()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                Reintentar
            </button>
        </div>

        {{-- Canvas del PDF --}}
        <div x-show="!loading && !error" class="page-transition" :class="{ loading: rendering }">
            <canvas id="pdf-canvas" class="rounded-lg bg-white"></canvas>
        </div>
    </main>

    {{-- ═══ BARRA INFERIOR DE NAVEGACIÓN ═══ --}}
    <footer class="flex-shrink-0 bg-gray-900 border-t border-gray-800 px-4 py-3 z-20">
        <div class="flex items-center justify-between max-w-2xl mx-auto gap-4">

            {{-- Página anterior --}}
            <button @click="prevPage()"
                    :disabled="currentPage <= 1 || rendering"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white text-sm font-medium rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="hidden sm:block">Anterior</span>
            </button>

            {{-- Selector de página --}}
            <div class="flex items-center gap-2">
                <span class="text-gray-500 text-sm">Página</span>
                <input type="number"
                       :value="currentPage"
                       :min="1"
                       :max="totalPages"
                       @change="goToPage($event.target.value)"
                       @keydown.enter="goToPage($event.target.value)"
                       class="w-14 px-2 py-1.5 text-center text-sm text-white bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"/>
                <span class="text-gray-500 text-sm">de</span>
                <span class="text-white text-sm font-semibold" x-text="totalPages"></span>
            </div>

            {{-- Página siguiente --}}
            <button @click="nextPage()"
                    :disabled="currentPage >= totalPages || rendering"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white text-sm font-medium rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                <span class="hidden sm:block">Siguiente</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Progreso móvil --}}
        <div class="sm:hidden flex items-center justify-center gap-2 mt-2">
            <span class="text-xs text-gray-500" x-text="percentage + '% leído'"></span>
        </div>
    </footer>
</body>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

function pdfReader() {
    let pdfDocument = null;
    return {
        // Estado
        pdf:         null,
        currentPage: {{ $readingProgress?->current_page ?? 1 }},
        totalPages:  0,
        scale:       1.2,
        loading:     true,
        rendering:   false,
        error:       null,
        saved:       false,
        isFullscreen: false,
        saveTimer:   null,
        savedTimer:  null,
        loadingText: 'Cargando documento...',

        // Config
        pdfUrl:      '{{ route("pdf.serve", ["type" => $type, "id" => $book->id]) }}',
        progressUrl: '{{ route("pdf.progress") }}',
        csrf:        document.querySelector('meta[name="csrf-token"]').content,
        itemId:      {{ $book->id }},
        itemType:    '{{ $type }}',
        lastPage:    {{ $readingProgress?->current_page ?? 1 }},

        async init() {
            this.loading     = true;
            this.error       = null;
            this.loadingText = 'Cargando documento...';

            try {
                const task = pdfjsLib.getDocument({
                    url: this.pdfUrl,
                    withCredentials: true,
                });

                task.onProgress = (data) => {
                    if (data.total) {
                        const pct = Math.round((data.loaded / data.total) * 100);
                        this.loadingText = `Cargando... ${pct}%`;
                    }
                };

                pdfDocument = await task.promise;
                this.totalPages = pdfDocument.numPages;

                // Ir a la última página guardada
                const startPage = Math.min(this.lastPage, this.totalPages);
                await this.renderPage(startPage);

                this.loading = false;

            } catch (e) {
                this.loading = false;
                this.error   = e.message || 'Error al cargar el archivo.';
            }

            // Escuchar fullscreen
            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
            });
        },

        async renderPage(num) {
            if (!pdfDocument || this.rendering) return;

            this.rendering = true;

            try {
                const page     = await pdfDocument.getPage(num);
                const canvas   = document.getElementById('pdf-canvas');
                const ctx      = canvas.getContext('2d');
                const viewport = page.getViewport({ scale: this.scale });

                canvas.width  = viewport.width;
                canvas.height = viewport.height;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                await page.render(renderContext).promise;

                // await page.render({ canvasContext: ctx, viewport }).promise;

                this.currentPage = num;

                // Scroll al inicio del área del PDF
                // const container = document.getElementById('pdf-container');
                // container.scrollTop = 0;
                const container = document.getElementById('pdf-container');
                if (container) {
                    container.scrollTop = 0;
                }

                // Guardar progreso automáticamente
                this.scheduleSave();

            } catch (e) {
                console.error('Error al renderizar página:', e);
            } finally {
                this.rendering = false;
            }
        },

        async nextPage() {
            if (this.currentPage < this.totalPages && !this.rendering) {
                await this.renderPage(this.currentPage + 1);
            }
        },

        async prevPage() {
            if (this.currentPage > 1 && !this.rendering) {
                await this.renderPage(this.currentPage - 1);
            }
        },

        async goToPage(val) {
            const num = parseInt(val);
            if (!isNaN(num) && num >= 1 && num <= this.totalPages && !this.rendering) {
                await this.renderPage(num);
            }
        },

        // Guardar con debounce de 800ms
        scheduleSave() {
            clearTimeout(this.saveTimer);
            this.saveTimer = setTimeout(() => this.saveProgress(), 800);
        },

        async saveProgress() {
            if (!this.totalPages) return;

            try {
                const res = await fetch(this.progressUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify({
                        type:         this.itemType,
                        id:           this.itemId,
                        current_page: this.currentPage,
                        total_pages:  this.totalPages,
                    }),
                });

                if (res.ok) {
                    // Mostrar indicador "Guardado" por 2 segundos
                    this.saved = true;
                    clearTimeout(this.savedTimer);
                    this.savedTimer = setTimeout(() => { this.saved = false; }, 2000);
                }
            } catch (e) {
                // Falla silenciosa — no interrumpir la lectura
            }
        },

        // Zoom
        zoomIn() {
            if (this.scale < 3) {
                this.scale = Math.round((this.scale + 0.25) * 100) / 100;
                this.renderPage(this.currentPage);
            }
        },

        zoomOut() {
            if (this.scale > 0.5) {
                this.scale = Math.round((this.scale - 0.25) * 100) / 100;
                this.renderPage(this.currentPage);
            }
        },

        cycleZoom() {
            const levels = [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 2.5, 3];
            const current = levels.findIndex(l => Math.abs(l - this.scale) < 0.01);
            const next    = (current + 1) % levels.length;
            this.scale    = levels[next];
            this.renderPage(this.currentPage);
        },

        fitToWidth() {
            if (!pdfDocument) return;
            this.pdf.getPage(this.currentPage).then(page => {
                const container     = document.getElementById('pdf-container');
                const unscaled      = page.getViewport({ scale: 1 });
                const availWidth    = container.clientWidth - 48;
                this.scale          = Math.round((availWidth / unscaled.width) * 100) / 100;
                this.renderPage(this.currentPage);
            });
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        },

        // Porcentaje de lectura
        get percentage() {
            if (!this.totalPages) return 0;
            return Math.round((this.currentPage / this.totalPages) * 100);
        },

        // Teclado
        handleKeydown(e) {
            const tag = e.target.tagName.toLowerCase();
            if (tag === 'input') return; // No interferir con inputs

            switch (e.key) {
                case 'ArrowRight':
                case 'ArrowDown':
                case ' ':
                    e.preventDefault();
                    this.nextPage();
                    break;
                case 'ArrowLeft':
                case 'ArrowUp':
                    e.preventDefault();
                    this.prevPage();
                    break;
                case '+':
                case '=':
                    this.zoomIn();
                    break;
                case '-':
                    this.zoomOut();
                    break;
                case 'f':
                case 'F':
                    this.toggleFullscreen();
                    break;
            }
        },
    };
}
</script>
</html>