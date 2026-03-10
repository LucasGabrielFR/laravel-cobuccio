@props(['title', 'value', 'color' => 'blue', 'icon'])

@php
    $colorOptions = [
        'blue' => [
            'bg' => 'bg-blue-100 dark:bg-blue-900/50',
            'text' => 'text-blue-600 dark:text-blue-400',
            'gradient' => 'from-blue-50/50 dark:from-blue-900/20'
        ],
        'emerald' => [
            'bg' => 'bg-emerald-100 dark:bg-emerald-900/50',
            'text' => 'text-emerald-600 dark:text-emerald-400',
            'gradient' => 'from-emerald-50/50 dark:from-emerald-900/20'
        ],
        'indigo' => [
            'bg' => 'bg-indigo-100 dark:bg-indigo-900/50',
            'text' => 'text-indigo-600 dark:text-indigo-400',
            'gradient' => 'from-indigo-50/50 dark:from-indigo-900/20'
        ],
        'rose' => [
            'bg' => 'bg-rose-100 dark:bg-rose-900/50',
            'text' => 'text-rose-600 dark:text-rose-400',
            'gradient' => 'from-rose-50/50 dark:from-rose-900/20'
        ],
        'orange' => [
            'bg' => 'bg-orange-100 dark:bg-orange-900/50',
            'text' => 'text-orange-600 dark:text-orange-400',
            'gradient' => 'from-orange-50/50 dark:from-orange-900/20'
        ],
    ];

    $selectedColor = $colorOptions[$color] ?? $colorOptions['blue'];
@endphp

<div class="p-6 bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/50 flex flex-col relative overflow-hidden group">
    <div class="absolute inset-0 bg-gradient-to-br {{ $selectedColor['gradient'] }} to-transparent dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
    <div class="flex items-center gap-4 relative z-10">
        <div class="p-3 {{ $selectedColor['bg'] }} {{ $selectedColor['text'] }} rounded-xl">
            {!! $icon !!}
        </div>
        <div>
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $value }}</p>
        </div>
    </div>
</div>
