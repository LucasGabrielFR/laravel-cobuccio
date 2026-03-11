@props(['id' => null, 'maxWidth' => 'md', 'title' => null])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    default => 'sm:max-w-md',
};
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-[60]"
    style="display: none;"
>
    <!-- Overlay/Backdrop -->
    <div 
        x-show="show"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" 
        aria-hidden="true"
    ></div>

    <!-- Scrollable Container -->
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-full p-4 text-center sm:p-0">
            
            <!-- Modal Panel -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-10 inline-block align-middle bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 {{ $maxWidthClass }} w-full border border-slate-200 dark:border-slate-700"
            >
                @if($title)
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between bg-white dark:bg-slate-800">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
                        <button @click="show = false" class="text-slate-400 hover:text-slate-500 transition-colors p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                <div class="p-6">
                    {{ $slot }}
                </div>

                @if(isset($footer))
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
