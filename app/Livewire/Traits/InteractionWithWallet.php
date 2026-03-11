<?php

namespace App\Livewire\Traits;

use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait InteractionWithWallet
{
    // Deposit State
    public bool $showDepositModal = false;
    public int $depositStep = 1;
    public $depositAmount = '';
    public string $pixKey = '';

    // Transfer State
    public bool $showTransferModal = false;
    public int $transferStep = 1;
    public string $transferEmail = '';
    public $transferAmount = '';
    public ?string $recipientName = null;
    public ?int $recipientId = null;

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

        $formattedAmount = number_format((float) $this->depositAmount, 2, '.', '');
        $this->pixKey = '00020126580014br.gov.bcb.pix0136' . Str::uuid() . '52040000530398654' . $formattedAmount . '5802BR5915COBUCCIO WALLET6009SAO PAULO62070503***6304' . rand(1000, 9999);
        
        $this->depositStep = 2;
    }

    public function confirmDeposit(TransactionService $transactionService)
    {
        $amount = (float) $this->depositAmount;
        $transactionService->deposit(Auth::user(), $amount);
        $this->closeDepositModal();
        session()->flash('message', 'Depósito de R$ ' . number_format($amount, 2, ',', '.') . ' realizado com sucesso!');
    }

    public function openTransferModal()
    {
        $this->reset(['transferEmail', 'transferAmount', 'recipientName', 'recipientId']);
        $this->transferStep = 1;
        $this->showTransferModal = true;
    }

    public function closeTransferModal()
    {
        $this->showTransferModal = false;
        $this->reset(['transferEmail', 'transferAmount', 'recipientName', 'recipientId']);
        $this->transferStep = 1;
    }

    public function verifyRecipientEmail(UserService $userService)
    {
        $this->validate([
            'transferEmail' => 'required|email'
        ], [
            'transferEmail.required' => 'Informe o e-mail do destinatário.',
            'transferEmail.email' => 'Informe um e-mail válido.',
        ]);

        $recipient = $userService->getUserByEmail($this->transferEmail);

        if (!$recipient || !$recipient->is_active) {
            $this->addError('transferEmail', 'Usuário inexistente ou inativo.');
            return;
        }

        if ($recipient->id === Auth::id()) {
            $this->addError('transferEmail', 'Você não pode transferir para si mesmo.');
            return;
        }

        $this->recipientName = $recipient->name;
        $this->recipientId = $recipient->id;
        $this->transferStep = 2;
    }

    public function reviewTransfer()
    {
        $user = Auth::user();
        $maxTransferAmount = $user->balance / 100;

        $this->validate([
            'transferAmount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($maxTransferAmount) {
                    if ((float)$value > $maxTransferAmount) {
                        $fail('Saldo insuficiente para esta transferência.');
                    }
                },
            ],
        ], [
            'transferAmount.required' => 'Informe um valor.',
            'transferAmount.numeric' => 'O valor deve ser numérico.',
            'transferAmount.min' => 'O mínimo é R$ 0,01.',
        ]);

        $this->transferStep = 3;
    }

    public function confirmTransfer(TransactionService $transactionService, UserService $userService)
    {
        $recipient = $userService->getUser($this->recipientId);

        if (!$recipient) {
            $this->closeTransferModal();
            return;
        }

        try {
            $amount = (float) $this->transferAmount;
            $name = $this->recipientName;

            $transactionService->transfer(Auth::user(), $recipient, $amount);
            
            $this->closeTransferModal();
            
            session()->flash('message', "Transferência de R$ " . number_format($amount, 2, ',', '.') . " para {$name} enviada com sucesso!");
        } catch (\Exception $e) {
            $this->addError('transferAmount', $e->getMessage());
        }
    }
}
