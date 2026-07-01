@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
    x-transition
    class="fixed top-20 right-4 z-50 flex items-center gap-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-xl shadow-lg max-w-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    <span class="text-sm font-medium">{{ session('success') }}</span>
    <button @click="show = false" class="ml-auto text-green-600 dark:text-green-400 hover:text-green-800">✕</button>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
    x-transition
    class="fixed top-20 right-4 z-50 flex items-center gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl shadow-lg max-w-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
    <span class="text-sm font-medium">{{ session('error') }}</span>
    <button @click="show = false" class="ml-auto text-red-600 dark:text-red-400 hover:text-red-800">✕</button>
</div>
@endif

@if(session('warning'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
    x-transition
    class="fixed top-20 right-4 z-50 flex items-center gap-3 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-xl shadow-lg max-w-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
    </svg>
    <span class="text-sm font-medium">{{ session('warning') }}</span>
    <button @click="show = false" class="ml-auto text-yellow-600 dark:text-yellow-400 hover:text-yellow-800">✕</button>
</div>
@endif