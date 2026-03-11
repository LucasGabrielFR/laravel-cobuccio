<?php

namespace App\Contracts;

use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;
    public function findById(int $id): ?Transaction;
    public function getPaginatedTransactions(?int $userId = null, int $perPage = 10);
    public function getPendingReversals();
}
