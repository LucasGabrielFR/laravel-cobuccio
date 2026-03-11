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
    /**
     * Realiza uma transferência entre usuários
     *
     * @param User $sender
     * @param User $receiver
     * @param float $amount
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function transfer(User $sender, User $receiver, float $amount)
    {
        if ($amount <= 0) {
            throw new \Exception('O valor da transferência deve ser maior que zero.');
        }

        if ($sender->id === $receiver->id) {
            throw new \Exception('Não é possível transferir para você mesmo.');
        }

        return DB::transaction(function () use ($sender, $receiver, $amount) {
            $amountInCents = (int) round($amount * 100);

            // Bloquear a linha do remetente atualizando (Pessimistic Locking) pode ser implementado em grandes escalas, 
            // mas para agora usaremos o saldo em memória carregado pela requisição atual.
            // Opcional no futuro: $sender = User::where('id', $sender->id)->lockForUpdate()->first();
            
            if ($sender->balance < $amountInCents) {
                throw new \Exception('Saldo insuficiente para realizar esta transferência.');
            }

            // 1. Deduzir saldo do rementente
            $newSenderBalance = $sender->balance - $amountInCents;
            $this->userRepository->update($sender->id, ['balance' => $newSenderBalance]);

            // 2. Adicionar saldo ao destinatário
            $newReceiverBalance = $receiver->balance + $amountInCents;
            $this->userRepository->update($receiver->id, ['balance' => $newReceiverBalance]);

            // 3. Registrar transação
            return $this->transactionRepository->create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $amountInCents,
                'type' => 'transfer',
                'status' => 'completed',
                'notes' => 'Transferência entre contas',
            ]);
        });
    }

    /**
     * Retorna o histórico de transações paginadas
     */
    public function getTransactions(?int $userId = null, int $perPage = 10)
    {
        return $this->transactionRepository->getPaginatedTransactions($userId, $perPage);
    }
}
