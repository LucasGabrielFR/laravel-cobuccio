<?php

namespace App\Repositories;

use App\Contracts\TransactionRepositoryInterface;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function getPaginatedTransactions(?int $userId = null, int $perPage = 10)
    {
        $query = Transaction::with(['sender', 'receiver'])->latest();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            });
        }

        return $query->paginate($perPage);
    }

    public function getPendingReversals()
    {
        return Transaction::with(['sender', 'receiver'])
            ->where('reversal_status', 'requested')
            ->latest()
            ->get();
    }
}
