@props(['id', 'label', 'model', 'placeholder' => '', 'rows' => 3])

<div class="space-y-1 w-full">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative">
        <textarea 
            id="{{ $id }}"
            wire:model="{{ $model }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            class="block w-full px-4 py-2 border rounded-xl text-sm focus:ring-2 focus:outline-none transition-all
                   bg-white dark:bg-slate-900 
                   text-slate-800 dark:text-slate-200 
                   placeholder-slate-400 dark:placeholder-slate-500
                   @error($model) 
                       border-red-300 dark:border-red-600/50 
                       focus:ring-red-200 dark:focus:ring-red-900/50 
                       focus:border-red-500 dark:focus:border-red-500 
                   @else 
                       border-slate-200 dark:border-slate-700 
                       focus:ring-blue-100 dark:focus:ring-blue-900/30 
                       focus:border-blue-500 dark:focus:border-blue-500 
                   @enderror"
        ></textarea>
        
        @error($model)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        @enderror
    </div>

    @error($model)
        <p class="text-xs text-red-500 mt-1" id="{{ $id }}-error">{{ $message }}</p>
    @enderror
</div>
