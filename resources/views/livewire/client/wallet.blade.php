<div class="p-6 md:p-10 space-y-8 max-w-5xl mx-auto">
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed top-5 right-5 z-[100] bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg border border-emerald-400 flex items-center gap-3 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-300">
                Minha Carteira
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Bem-vindo, {{ $user->name }}.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button wire:click="logout" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 active:scale-95 transition-all duration-200 flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sair
            </button>
        </div>
    </header>

    <!-- Main Wallet Card -->
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Decorative bg -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-black/10 blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div>
                <p class="text-emerald-100 font-medium mb-1 text-sm uppercase tracking-wider">Saldo Disponível</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold opacity-80">R$</span>
                    <span class="text-5xl font-black tracking-tight">{{ number_format($user->balance / 100, 2, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <button wire:click="openDepositModal" class="flex-1 sm:flex-none px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 rounded-xl font-semibold text-white active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                    Depositar
                </button>
                <button wire:click="openTransferModal" class="flex-1 sm:flex-none px-6 py-3 bg-white text-emerald-700 hover:bg-emerald-50 rounded-xl font-bold shadow-lg active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Transferir
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Info -->
    <div class="bg-white dark:bg-slate-800/90 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/60">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Transações Recentes</h2>
        </div>
        <div class="p-6 text-center text-slate-500 dark:text-slate-400">
            <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p>Histórico de transações será implementado em breve.</p>
        </div>
    </div>

    <!-- Deposit Modal -->
    <x-modal wire:model="showDepositModal" title="Adicionar Fundos" on-close="closeDepositModal">
        @if($depositStep === 1)
            <!-- Step 1: Selecionar Valor -->
            <div class="space-y-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Informe o valor que deseja depositar na sua carteira. A transferência deve ser feita via PIX.
                </p>
                <x-input-group 
                    id="depositAmount" 
                    label="Valor do Depósito (R$)" 
                    type="number" 
                    step="0.01" 
                    min="1" 
                    wire:model="depositAmount" 
                    placeholder="0.00" 
                    autofocus 
                />
            </div>
            
            <x-slot name="footer">
                <button wire:click="closeDepositModal" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-all text-slate-700 dark:text-slate-300">
                    Cancelar
                </button>
                <button wire:click="generatePix" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    Avançar para Pagamento
                </button>
            </x-slot>

        @elseif($depositStep === 2)
            <!-- Step 2: Efetuar Pagamento PIX -->
            <div class="space-y-5 text-center flex flex-col items-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Sua chave PIX Copia e Cola foi gerada! No mundo real você usaria seu app do banco para pagar.
                </p>

                <div class="p-4 bg-white dark:bg-slate-800 border-2 border-emerald-500 border-dashed rounded-xl w-48 h-48 flex items-center justify-center">
                    <!-- Fake QR Code SVG -->
                    <svg class="w-full h-full text-slate-800 dark:text-slate-300" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h-3v2h3v-2zm-3 4h3v2h-3v-2zm-3-2h2v2h-2v-2zm0-2h2v2h-2v-2zm3-2h-3v2h3v-2zm3 2h2v2h-2v-2zm-6 4h2v2h-2v-2zm3 2h-3v2h3v-2zm3-2h2v2h-2v-2zm0 2h-2v2h2v-2z" />
                    </svg>
                </div>

                <div class="w-full" x-data="{ copied: false }">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 text-left">PIX Copia e Cola:</label>
                    <div class="relative group">
                        <textarea 
                            readonly 
                            id="pixKeyArea"
                            class="block w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-600 dark:text-slate-300 focus:outline-none resize-none overflow-hidden leading-relaxed shadow-inner"
                            rows="3"
                        >{{ $pixKey }}</textarea>
                        
                        <button 
                            @click="
                                navigator.clipboard.writeText($refs.pixInput.value); 
                                copied = true; 
                                setTimeout(() => copied = false, 2000)
                            "
                            x-ref="pixInput"
                            value="{{ $pixKey }}"
                            type="button"
                            class="absolute top-2 right-2 p-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg shadow-sm transition-all active:scale-90 flex items-center gap-1.5"
                            :class="{ 'bg-blue-500 hover:bg-blue-600': copied }"
                        >
                            <template x-if="!copied">
                                <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Copiar
                                </span>
                            </template>
                            <template x-if="copied">
                                <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Copiado!
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex w-full justify-between items-center">
                    <button wire:click="$set('depositStep', 1)" class="px-4 py-2 border-none rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-600 dark:text-slate-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Voltar
                    </button>
                    <!-- Simulated confirmation button since this is a test/demo app -->
                    <button wire:click="confirmDeposit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm active:scale-95 transition-all relative overflow-hidden group">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Simular Confirmação do App do Banco
                        </span>
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    </button>
                </div>
            </x-slot>
        @endif
    </x-modal>
</div>
