<div class="p-6 md:p-10 space-y-8">
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed top-5 right-5 z-[100] bg-blue-600 text-white px-6 py-3 rounded-xl shadow-lg border border-blue-500 flex items-center gap-3 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-500 dark:from-blue-400 dark:to-indigo-300">
                Dashboard
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">
                Olá, <span class="font-semibold text-slate-700 dark:text-slate-200">{{ Auth::user()->name }}</span>.
                <span class="block text-xs opacity-75">{{ Auth::user()->email }}</span>
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="hidden md:flex flex-col items-end mr-4 pr-4 border-r border-slate-200 dark:border-slate-700">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Meu Saldo</span>
                <span class="text-lg font-bold text-slate-900 dark:text-white">R$ {{ number_format($currentUser->balance / 100, 2, ',', '.') }}</span>
            </div>
            
            <button wire:click="openDepositModal" class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg hover:bg-emerald-100 transition-all active:scale-90" title="Depositar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
            </button>
            <button wire:click="openTransferModal" class="p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 transition-all active:scale-90" title="Transferir">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </button>

            <button wire:click="logout" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 active:scale-95 transition-all duration-200 flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sair
            </button>
            <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 active:scale-95 transition-all duration-200 flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Usuário
            </button>
        </div>
    </header>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-admin.stat-card 
            title="Total de Usuários" 
            value="{{ $stats['total_users'] }}" 
            color="blue"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card 
            title="Usuários Ativos" 
            value="{{ $stats['active_users'] }}" 
            color="emerald"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card 
            title="Meu Saldo" 
            value="R$ {{ number_format($currentUser->balance / 100, 2, ',', '.') }}" 
            color="indigo"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot>
        </x-admin.stat-card>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-800/90 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Controle de Usuários</h2>
            
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <!-- Status Filter -->
                <div class="w-full sm:w-48">
                    <select wire:model.live="filterActive" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-200 transition-all">
                        <option value="all">Todos os Status</option>
                        <option value="active">Somente Ativos</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar usuários..." class="pl-10 pr-4 py-2 w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-200 transition-all">
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-xs text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/20 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-medium">Usuário</th>
                        <th class="px-6 py-4 font-medium">E-mail</th>
                        <th class="px-6 py-4 font-medium">Perfil</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Cadastrado em</th>
                        <th class="px-6 py-4 font-medium">Atualizado em</th>
                        <th class="px-6 py-4 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-400 flex items-center justify-center text-white font-bold text-xs shadow-inner">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400 border border-slate-200 dark:border-slate-800">
                                        Cliente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                {{ $user->updated_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="edit({{ $user->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 active:scale-90 transition-all duration-200 p-1" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button wire:click="confirmToggleStatus({{ $user->id }})" class="{{ $user->is_active ? 'text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300' : 'text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300' }} active:scale-90 transition-all duration-200 p-1 ml-2" title="{{ $user->is_active ? 'Desativar' : 'Ativar' }}">
                                    @if($user->is_active)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/20">
                                Nenhum usuário encontrado no sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-900/20">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Global Transactions Table -->
    <div class="bg-white dark:bg-slate-800/90 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/60">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Histórico Global de Transações</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Visão geral de todas as movimentações do sistema.</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-xs text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/20 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-medium">Data/Hora</th>
                        <th class="px-6 py-4 font-medium">Tipo</th>
                        <th class="px-6 py-4 font-medium">Origem</th>
                        <th class="px-6 py-4 font-medium">Destino</th>
                        <th class="px-6 py-4 font-medium text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                <div class="font-medium text-slate-700 dark:text-slate-300">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs">{{ $transaction->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($transaction->type === 'deposit')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                        Depósito PIX
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        Transferência
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($transaction->type === 'deposit')
                                    <span class="text-slate-400 dark:text-slate-500 italic">Sistema (PIX)</span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold">{{ substr($transaction->sender->name, 0, 1) }}</div>
                                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $transaction->sender->name }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold">{{ substr($transaction->receiver->name, 0, 1) }}</div>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $transaction->receiver->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $transaction->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $transaction->type === 'deposit' ? '+' : '-' }} R$ {{ number_format($transaction->amount / 100, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/20">
                                Nenhuma transação registrada no sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-900/20">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
    <!-- User Form Modal -->
    <x-modal wire:model="showModal" :title="$editingUserId ? 'Editar Usuário' : 'Novo Usuário'">
        <form wire:submit="save">
            <div class="space-y-4">
                <!-- Nome -->
                <x-input-group id="name" model="name" label="Nome" />

                <!-- Email -->
                <x-input-group id="email" model="email" label="E-mail" type="email" />

                <!-- Perfil -->
                <x-select-group 
                    id="role" 
                    model="role" 
                    label="Perfil" 
                    :options="['admin' => 'Administrador', 'client' => 'Cliente']" 
                />

                <!-- Password -->
                <x-input-group id="password" model="password" label="Senha {{ $editingUserId ? '(Deixe em branco para manter)' : '' }}" type="password" />

                <!-- Ativo / Inativo Toggle -->
                <div class="flex items-center gap-3 pt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Usuário Ativo</span>
                    </label>
                </div>
            </div>
            
            <x-slot name="footer">
                <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 active:scale-95 transition-all duration-200 focus:outline-none dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                    Cancelar
                </button>
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 active:scale-95 transition-all duration-200 focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <span wire:loading wire:target="save">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                    Salvar
                </button>
            </x-slot>
        </form>
    </x-modal>

    <!-- Confirm Toggle Status Modal -->
    <x-modal wire:model="showConfirmModal" title="Confirmar Ação">
        <div class="space-y-3">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30 mb-4">
                <svg class="h-8 w-8 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                Você tem certeza que deseja alterar o status deste usuário? Esta ação impactará o acesso imediato ao sistema.
            </p>
        </div>

        <x-slot name="footer">
            <button wire:click="cancelToggleStatus" type="button" class="px-4 py-2 w-full text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 active:scale-95 transition-all duration-200 focus:outline-none dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                Cancelar
            </button>
            <button wire:click="performToggleStatus" type="button" class="px-5 py-2 w-full text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 active:scale-95 transition-all duration-200 focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600">
                Confirmar
            </button>
        </x-slot>
    </x-modal>

    @include('livewire.partials.wallet-modals', ['balance' => $currentUser->balance])
</div>
