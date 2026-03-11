<div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative Blobs -->
    <div class="absolute top-0 left-0 md:left-1/4 w-96 h-96 bg-blue-500/10 dark:bg-blue-600/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute top-0 right-0 md:right-1/4 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-600/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-600/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>

    <div class="max-w-md w-full relative z-10">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white shadow-lg mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-500 dark:from-blue-400 dark:to-indigo-300">
                Acessar Conta
            </h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Bem-vindo de volta! Insira suas credenciais abaixo.
            </p>
        </div>

        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-8 rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/50">
            <form wire:submit="login" class="space-y-6">
                
                <x-input-group id="email" model="email" label="E-mail" type="email" autocomplete="email" required autofocus />
                <x-input-group id="password" model="password" label="Senha" type="password" autocomplete="current-password" required />

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" wire:model="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded dark:border-slate-600 dark:bg-slate-700">
                        <label for="remember" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">
                            Lembrar de mim
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                            Esqueceu a senha?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 active:scale-95 transition-all duration-200">
                        Entrar na Plataforma
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
                Ainda não tem conta? 
                <a href="{{ route('register') }}" wire:navigate class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    Crie uma aqui
                </a>
            </div>
        </div>
    </div>
</div>
