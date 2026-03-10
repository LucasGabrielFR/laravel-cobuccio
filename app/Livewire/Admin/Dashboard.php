<?php

namespace App\Livewire\Admin;

use App\Services\UserService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    public function render(UserService $userService)
    {
        return view('livewire.admin.dashboard', [
            'users' => $userService->getPaginatedUsers(10),
            'stats' => $userService->getDashboardStats(),
        ]);
    }
}
