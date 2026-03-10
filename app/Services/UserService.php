<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getPaginatedUsers(int $perPage = 10)
    {
        return $this->userRepository->getAllPaginated($perPage);
    }

    public function getDashboardStats(): array
    {
        return [
            'total_users' => $this->userRepository->getTotalUsersCount(),
        ];
    }

    public function getUser(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data)
    {
        return $this->userRepository->update($id, $data);
    }
}
