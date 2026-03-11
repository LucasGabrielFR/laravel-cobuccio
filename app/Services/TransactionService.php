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

    /**
     * Solicita o estorno de uma transação enviada por um usuário
     */
    public function requestReversal(int $transactionId, int $userId, string $reason)
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            throw new \Exception('Transação não encontrada.');
        }

        if ($transaction->sender_id !== $userId) {
            throw new \Exception('Apenas o remetente pode solicitar o estorno desta transação.');
        }

        if ($transaction->type !== 'transfer') {
            throw new \Exception('Apenas transferências podem ser estornadas.');
        }

        if ($transaction->reversal_status !== 'none') {
            throw new \Exception('Já existe uma solicitação de estorno ou ela já foi processada.');
        }

        $transaction->update([
            'reversal_status' => 'requested',
            'reversal_reason' => $reason,
        ]);

        return $transaction;
    }

    /**
     * Aprova o estorno de uma transação
     */
    public function approveReversal(int $transactionId)
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction || $transaction->reversal_status !== 'requested') {
            throw new \Exception('Solicitação de estorno inválida ou não encontrada.');
        }

        return DB::transaction(function () use ($transaction) {
            $sender = $this->userRepository->findById($transaction->sender_id);
            $receiver = $this->userRepository->findById($transaction->receiver_id);

            // Se o recebedor já não tiver saldo suficiente, a transação lançaria um aviso?
            // Vamos apenas cobrar e pode ficar negativo, ou a regra é bloquear se não tiver?
            // Como é um banco simulado, se o estorno for aprovado, o sistema apenas debita mesmo que fique negativo ($receiver).
            // Porém o User model ou Service de user precisará permitir saldo negativo ou tratar isso.
            // Para manter simples, debitamos o valor.
            
            $newReceiverBalance = $receiver->balance - $transaction->amount;
            $this->userRepository->update($receiver->id, ['balance' => $newReceiverBalance]);

            $newSenderBalance = $sender->balance + $transaction->amount;
            $this->userRepository->update($sender->id, ['balance' => $newSenderBalance]);

            $transaction->update([
                'reversal_status' => 'approved',
            ]);

            // Cria uma nova transação que registra a devolução de forma clara
            $this->transactionRepository->create([
                'sender_id' => $receiver->id,
                'receiver_id' => $sender->id,
                'amount' => $transaction->amount,
                'type' => 'transfer',
                'status' => 'completed',
                'notes' => 'Estorno da transação #' . $transaction->id,
                'related_transaction_id' => $transaction->id,
            ]);

            return $transaction;
        });
    }

    /**
     * Rejeita o estorno de uma transação
     */
    public function rejectReversal(int $transactionId)
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction || $transaction->reversal_status !== 'requested') {
            throw new \Exception('Solicitação de estorno inválida ou não encontrada.');
        }

        $transaction->update([
            'reversal_status' => 'rejected',
        ]);

        return $transaction;
    }

    public function getPendingReversals()
    {
        return $this->transactionRepository->getPendingReversals();
    }
}
