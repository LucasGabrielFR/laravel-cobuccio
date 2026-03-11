<?php

namespace App\Livewire\Client;

use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

use App\Livewire\Traits\InteractionWithWallet;

use Livewire\WithPagination;

class Wallet extends Component
{
    use InteractionWithWallet;
    use WithPagination;

    #[Layout('layouts.app')]
    public function render(UserService $userService, TransactionService $transactionService)
    {
        return view('livewire.client.wallet', [
            'user' => $userService->getUser(Auth::id()),
            'transactions' => $transactionService->getTransactions(Auth::id(), 5),
        ]);
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }
}
