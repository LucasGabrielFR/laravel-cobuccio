<?php

namespace App\Contracts;

use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;
    public function findById(int $id): ?Transaction;
}
