<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, ?string $search = null, ?bool $activeOnly = null): LengthAwarePaginator;
    public function getTotalUsersCount(): int;
    public function getActiveUsersCount(): int;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
