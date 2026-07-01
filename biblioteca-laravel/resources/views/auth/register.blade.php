<!DOCTYPE html>
<html lang="es"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro — Biblioteca</title>
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Biblioteca
            </a>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                Crea tu cuenta de estudiante
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm">
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Crear cuenta</h1>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Ej: María García"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border
                                  {{ $errors->has('name') ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700' }}
                                  bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Correo electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="tu@correo.com"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border
                                  {{ $errors->has('email') ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700' }}
                                  bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Carnet (opcional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Carnet estudiantil
                        <span class="text-gray-400 text-xs">(opcional)</span>
                    </label>
                    {{-- Placeholder actualizado al año 2026 --}}
                    <input type="text" name="student_id" value="{{ old('student_id') }}"
                        placeholder="Ej: EST-2026-001"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border
                                  {{ $errors->has('student_id') ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700' }}
                                  bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    @error('student_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono (opcional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Teléfono
                        <span class="text-gray-400 text-xs">(opcional)</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="Ej: +57 300 123 4567"
                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>

                {{-- Contraseña --}}
                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contraseña <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'"
                            name="password" required
                            placeholder="Mínimo 8 caracteres"
                            class="w-full pl-4 pr-10 py-2.5 text-sm rounded-xl border
                                      {{ $errors->has('password') ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700' }}
                                      bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        {{-- Accesibilidad: aria-label añadido --}}
                        <button type="button" @click="show = !show" aria-label="Mostrar u ocultar contraseña"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar contraseña --}}
                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Confirmar contraseña <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'"
                            name="password_confirmation" required
                            placeholder="Repite tu contraseña"
                            class="w-full pl-4 pr-10 py-2.5 text-sm rounded-xl border 
                                      {{ $errors->has('password_confirmation') ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-700' }}
                                      bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        {{-- Accesibilidad: aria-label añadido --}}
                        <button type="button" @click="show = !show" aria-label="Mostrar u ocultar confirmación de contraseña"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    {{-- Directiva @error añadida para confirmación de contraseña --}}
                    @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón --}}
                <button type="submit"
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors mt-2">
                    Crear cuenta
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}"
                    class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                    Iniciar sesión
                </a>
            </p>
        </div>

        {{-- Dark mode toggle --}}
        <div class="text-center mt-4">
            {{-- Accesibilidad: aria-label añadido --}}
            <button @click="darkMode = !darkMode" aria-label="Alternar modo oscuro"
                class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <span x-show="!darkMode">Cambiar a modo oscuro</span>
                <span x-show="darkMode">Cambiar a modo claro</span>
            </button>
        </div>
    </div>
</body>

</html>