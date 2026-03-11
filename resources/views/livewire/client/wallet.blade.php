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
            <p class="text-slate-500 dark:text-slate-400 mt-1">
                Bem-vindo, <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $user->name }}</span>.
                <span class="block text-xs opacity-75">{{ $user->email }}</span>
            </p>
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
        
        @if($transactions->count() > 0)
            <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                @foreach($transactions as $transaction)
                    @php
                        $isDeposit = $transaction->type === 'deposit';
                        $isReceiver = $transaction->receiver_id === $user->id;
                        $isPositive = $isDeposit || $isReceiver;
                    @endphp
                    <div class="p-4 sm:px-6 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $isPositive ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400' }}">
                                @if($isDeposit)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                @elseif($isReceiver)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                                    @if($isDeposit)
                                        Depósito via PIX
                                    @elseif($isReceiver)
                                        Transferência recebida de {{ $transaction->sender->name }}
                                    @else
                                        Transferência enviada para {{ $transaction->receiver->name }}
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $transaction->created_at->format('d/m/Y \à\s H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 flex flex-col items-end gap-1">
                            <p class="text-sm font-bold {{ $isPositive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $isPositive ? '+' : '-' }} R$ {{ number_format($transaction->amount / 100, 2, ',', '.') }}
                            </p>
                            @if(!$isPositive && $transaction->type === 'transfer')
                                @if($transaction->reversal_status === 'none')
                                    <button wire:click="openReversalModal({{ $transaction->id }})" class="text-[10px] text-orange-500 hover:text-orange-600 uppercase font-semibold tracking-wider hover:underline transition-all cursor-pointer">
                                        Solicitar Estorno
                                    </button>
                                @elseif($transaction->reversal_status === 'requested')
                                    <span class="text-[10px] text-amber-500 uppercase font-semibold tracking-wider">
                                        Estorno em Análise
                                    </span>
                                @elseif($transaction->reversal_status === 'approved')
                                    <span class="text-[10px] text-emerald-500 uppercase font-semibold tracking-wider">
                                        Estornado
                                    </span>
                                @elseif($transaction->reversal_status === 'rejected')
                                    <span class="text-[10px] text-red-500 uppercase font-semibold tracking-wider">
                                        Estorno Negado
                                    </span>
                                @endif
                            @else
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-semibold tracking-wider">
                                    Concluído
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/60">
                    {{ $transactions->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        @else
            <div class="p-10 text-center text-slate-500 dark:text-slate-400 flex flex-col items-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 text-slate-400 dark:text-slate-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-medium">Nenhuma transação encontrada</p>
                <p class="text-xs mt-1 opacity-70">Seus depósitos e transferências aparecerão aqui.</p>
            </div>
        @endif
    </div>

    @include('livewire.partials.wallet-modals', ['balance' => $user->balance])
</div>
