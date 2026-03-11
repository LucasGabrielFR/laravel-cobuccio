@props(['id', 'label', 'type' => 'text', 'model' => null])

@php
    $modelName = $model ?? $attributes->wire('model')->value();
@endphp

<div x-data="{ show: false }">
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}
    </label>
    
    <div class="relative mt-1">
        <input 
            @if($type === 'password') :type="show ? 'text' : 'password'" @else type="{{ $type }}" @endif
            id="{{ $id }}" 
            @if($model) wire:model="{{ $model }}" @endif
            {{ $attributes->merge(['class' => 'block w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm shadow-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:text-slate-100' . ($type === 'password' ? ' pr-10' : '')]) }}
        >
        
        @if($type === 'password')
            <button 
                type="button" 
                @click="show = !show" 
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-500 focus:outline-none"
            >
                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.047m4.5 4.5a3 3 0 003.758 3.758M9 9l.269-.269m5 5l.269-.269M17.615 17.615l-1.015-1.015M6.708 6.708L5.226 5.226M9 10.133V9c0-.833.333-1.667 1-2.333m5 5L21 21m-2.125-2.125l-2.031-2.031M15.465 15.465l-1.591-1.591M14 14l-.269.269" />
                </svg>
            </button>
        @endif
    </div>
    
    @if($modelName)
        @error($modelName) 
            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
        @enderror
    @endif
</div>
