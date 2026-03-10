@props(['show' => false, 'maxWidth' => 'sm'])

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

@if($show)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full {{ $maxWidthClass }} mx-4 overflow-hidden border border-slate-200 dark:border-slate-700 relative">
            {{ $slot }}
        </div>
    </div>
@endif
