<div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative Blobs -->
    <div class="absolute top-0 right-0 md:right-1/4 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-600/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute bottom-0 left-0 md:left-1/4 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-600/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>

    <div class="max-w-md w-full relative z-10">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white shadow-lg mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-300">
                Criar Conta
            </h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Junte-se a nós e gerencie sua carteira facilmente.
            </p>
        </div>

        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-8 rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/50">
            <form wire:submit="register" class="space-y-4">
                
                <x-input-group id="name" model="name" label="Nome Completo" type="text" autocomplete="name" required autofocus />
                
                <x-input-group id="email" model="email" label="E-mail" type="email" autocomplete="email" required />

                <x-input-group id="password" model="password" label="Senha" type="password" autocomplete="new-password" required />
                
                <x-input-group id="password_confirmation" model="password_confirmation" label="Confirme a Senha" type="password" required />

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 active:scale-95 transition-all duration-200">
                        Registrar
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
                Já possui uma conta? 
                <a href="{{ route('login') }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
                    Faça login
                </a>
            </div>
        </div>
    </div>
</div>
