@props(['id', 'label', 'type' => 'text', 'model'])

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}
    </label>
    
    <input 
        type="{{ $type }}" 
        id="{{ $id }}" 
        wire:model="{{ $model }}" 
        {{ $attributes->merge(['class' => 'mt-1 block w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm shadow-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:text-slate-100']) }}
    >
    
    @error($model) 
        <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
    @enderror
</div>
