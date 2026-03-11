<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getPaginatedUsers(int $perPage = 10, ?string $search = null, ?bool $activeOnly = null)
    {
        return $this->userRepository->getAllPaginated($perPage, $search, $activeOnly);
    }

    public function getDashboardStats(): array
    {
        return [
            'total_users' => $this->userRepository->getTotalUsersCount(),
            'active_users' => $this->userRepository->getActiveUsersCount(),
        ];
    }

    public function getUser(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
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
