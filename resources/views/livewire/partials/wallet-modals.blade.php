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

<!-- Transfer Modal -->
<x-modal wire:model="showTransferModal" title="Nova Transferência">
    @if($transferStep === 1)
        <div class="space-y-4">
            <p class="text-sm text-slate-500 dark:text-slate-400">Informe o e-mail da pessoa para quem deseja enviar dinheiro.</p>
            <x-input-group id="transferEmail" model="transferEmail" label="E-mail do destinatário" type="email" placeholder="email@exemplo.com" />
        </div>
        
        <x-slot name="footer">
            <button type="button" wire:click="closeTransferModal" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 transition-all dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                Cancelar
            </button>
            <button type="button" wire:click="verifyRecipientEmail" wire:loading.attr="disabled" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 transition-all flex items-center gap-2">
                <span wire:loading wire:target="verifyRecipientEmail">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
                Continuar
            </button>
        </x-slot>
    @elseif($transferStep === 2)
        <div class="space-y-4">
            <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold uppercase">
                    {{ substr($recipientName, 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $recipientName }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $transferEmail }}</p>
                </div>
            </div>

            <p class="text-sm text-slate-500 dark:text-slate-400">Excelente! Qual o valor que deseja transferir?</p>
            <x-input-group id="transferAmount" model="transferAmount" label="Valor da Transferência (R$)" type="number" step="0.01" />

            <div class="flex justify-between items-center text-xs mt-2 px-1">
                <span class="text-slate-500 font-medium">Saldo Atual:</span>
                <span class="font-bold {{ $balance > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600' }}">R$ {{ number_format($balance / 100, 2, ',', '.') }}</span>
            </div>
        </div>
        
        <x-slot name="footer">
            <div class="flex w-full justify-between items-center">
                <button wire:click="$set('transferStep', 1)" class="px-4 py-2 border-none rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-600 dark:text-slate-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Voltar
                </button>
                <button type="button" wire:click="reviewTransfer" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 transition-all">
                    Revisar
                </button>
            </div>
        </x-slot>
    @elseif($transferStep === 3)
        <div class="space-y-4">
            <div class="text-center pb-4 border-b border-slate-200 dark:border-slate-700">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Valor</p>
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white">R$ {{ number_format((float)$transferAmount, 2, ',', '.') }}</h2>
            </div>

            <div class="py-2 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500">Para</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $recipientName }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500">Banco</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">Cobuccio Wallet</span>
                </div>
            </div>

            @if($errors->has('transferAmount'))
                <div class="p-3 mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg flex items-start gap-2">
                    <svg class="flex-shrink-0 w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $errors->first('transferAmount') }}</span>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex w-full justify-between items-center">
                <button wire:click="$set('transferStep', 2)" class="px-4 py-2 border-none rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-600 dark:text-slate-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Voltar
                </button>
                <button type="button" wire:click="confirmTransfer" wire:loading.attr="disabled" class="px-5 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg shadow-sm hover:bg-emerald-700 active:scale-95 transition-all flex items-center gap-2">
                    <span wire:loading wire:target="confirmTransfer">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                    Confirmar Transferência
                </button>
            </div>
        </x-slot>
    @endif
</x-modal>
