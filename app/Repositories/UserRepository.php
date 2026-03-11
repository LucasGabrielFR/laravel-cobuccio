<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, ?string $search = null, ?bool $activeOnly = null): LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($activeOnly === true) {
            $query->where('is_active', true);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getTotalUsersCount(): int
    {
        return User::count();
    }

    public function getActiveUsersCount(): int
    {
        return User::where('is_active', true)->count();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return User::destroy($id) > 0;
    }
}
