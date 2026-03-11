<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-slate-50 text-slate-900 antialiased dark:bg-slate-900 dark:text-slate-100 flex flex-col min-h-screen">
        <!-- Optional Topbar/Navbar could go here later -->
        <main class="flex-grow">
            {{ $slot }}
        </main>
        
        @livewireScripts
        <style>
            @keyframes pulse-slow {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.95; transform: scale(1.005); }
            }
            .animate-pulse-slow {
                animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
        </style>
    </body>
</html>
