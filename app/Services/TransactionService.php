<?php

namespace App\Services;

use App\Contracts\TransactionRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Realiza o depósito na conta de um usuário
     *
     * @param User $user
     * @param float $amount
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function deposit(User $user, float $amount)
    {
        if ($amount <= 0) {
            throw new \Exception('O valor do depósito deve ser maior que zero.');
        }

        return DB::transaction(function () use ($user, $amount) {
            // Converter para centavos
            $amountInCents = (int) round($amount * 100);

            // 1. Atualizar saldo do usuário
            $newBalance = $user->balance + $amountInCents;
            $this->userRepository->update($user->id, ['balance' => $newBalance]);

            // 2. Registrar transação
            return $this->transactionRepository->create([
                'sender_id' => null, // Depósito não tem remetente no sistema
                'receiver_id' => $user->id,
                'amount' => $amountInCents,
                'type' => 'deposit',
                'status' => 'completed',
                'notes' => 'Depósito via PIX simulação',
            ]);
        });
    }
}
