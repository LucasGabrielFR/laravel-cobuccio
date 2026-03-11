<?php

namespace App\Livewire\Client;

use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Wallet extends Component
{
    #[Layout('layouts.app')]
    public function render(UserService $userService)
    {
        return view('livewire.client.wallet', [
            'user' => $userService->getUser(Auth::id()),
        ]);
    }

    // Deposit State
    public bool $showDepositModal = false;
    public int $depositStep = 1;
    public $depositAmount = '';
    public string $pixKey = '';

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }

    public function openDepositModal()
    {
        $this->reset(['depositAmount', 'pixKey']);
        $this->depositStep = 1;
        $this->showDepositModal = true;
    }

    public function closeDepositModal()
    {
        $this->showDepositModal = false;
        $this->reset(['depositAmount', 'pixKey']);
        $this->depositStep = 1;
    }

    public function generatePix()
    {
        $this->validate([
            'depositAmount' => 'required|numeric|min:0.01|max:1000000',
        ], [
            'depositAmount.required' => 'Informe um valor.',
            'depositAmount.numeric' => 'O valor deve ser numérico.',
            'depositAmount.min' => 'O mínimo é R$ 0,01.',
            'depositAmount.max' => 'Valor muito alto para esta operação.',
        ]);

        // Simulação da geração de um Copy & Paste PIX
        $formattedAmount = number_format((float) $this->depositAmount, 2, '.', '');
        $this->pixKey = '00020126580014br.gov.bcb.pix0136' . Str::uuid() . '52040000530398654' . $formattedAmount . '5802BR5915COBUCCIO WALLET6009SAO PAULO62070503***6304' . rand(1000, 9999);
        
        $this->depositStep = 2;
    }

    public function confirmDeposit(TransactionService $transactionService)
    {
        // Conclui o depósito no serviço, que já atualiza o saldo e gera o registro
        $transactionService->deposit(Auth::user(), (float) $this->depositAmount);
        
        $this->closeDepositModal();
        session()->flash('message', 'Depósito realizado com sucesso!');
    }

    public function openTransferModal()
    {
        // Placeholder para próxima etapa
    }
}
