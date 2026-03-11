<?php

namespace App\Livewire\Admin;

use App\Services\UserService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Url;

use App\Livewire\Traits\InteractionWithWallet;

class Dashboard extends Component
{
    use WithPagination;
    use InteractionWithWallet;

    #[Layout('layouts.app')]
    public function render(UserService $userService, TransactionService $transactionService)
    {
        return view('livewire.admin.dashboard', [
            'users' => $userService->getPaginatedUsers(10, $this->search, $this->filterActive === 'active'),
            'stats' => $userService->getDashboardStats(),
            'currentUser' => $userService->getUser(Auth::id()),
            'transactions' => $transactionService->getTransactions(null, 5),
        ]);
    }

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $filterActive = 'all'; // all, active

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterActive()
    {
        $this->resetPage();
    }

    // Modal & Form State
    public bool $showModal = false;
    public ?int $editingUserId = null;
    
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'client';
    public bool $is_active = true;

    // Confirm Modal State
    public bool $showConfirmModal = false;
    public ?int $confirmingUserId = null;

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->editingUserId,
            'password' => [
                $this->editingUserId ? 'nullable' : 'required',
                'string',
            ],
            'role' => 'required|in:admin,client',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'password.required' => 'A senha é obrigatória.',
        ];
    }

    public function create()
    {
        $this->reset(['editingUserId', 'name', 'email', 'password', 'role', 'is_active']);
        $this->role = 'client'; // Ensure default
        $this->showModal = true;
    }

    public function edit(int $id, UserService $userService)
    {
        $user = $userService->getUser($id);
        if ($user) {
            $this->editingUserId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role ?? 'client';
            $this->password = ''; // Don't fill password
            $this->is_active = $user->is_active ?? true;
            
            $this->showModal = true;
        }
    }

    public function save(UserService $userService)
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->editingUserId) {
            $userService->updateUser($this->editingUserId, $data);
            session()->flash('message', 'Usuário atualizado com sucesso!');
        } else {
            $userService->createUser($data);
            session()->flash('message', 'Usuário criado com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['editingUserId', 'name', 'email', 'password', 'role', 'is_active']);
    }

    public function confirmToggleStatus(int $id)
    {
        $this->confirmingUserId = $id;
        $this->showConfirmModal = true;
    }

    public function performToggleStatus(UserService $userService)
    {
        if ($this->confirmingUserId) {
            $user = $userService->getUser($this->confirmingUserId);
            if ($user) {
                $newStatus = !$user->is_active;
                $userService->updateUser($this->confirmingUserId, ['is_active' => $newStatus]);
                session()->flash('message', 'Status do usuário alterado para ' . ($newStatus ? 'Ativo' : 'Inativo') . '!');
            }
        }
        $this->showConfirmModal = false;
        $this->confirmingUserId = null;
    }
    
    public function cancelToggleStatus()
    {
        $this->showConfirmModal = false;
        $this->confirmingUserId = null;
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }
}
