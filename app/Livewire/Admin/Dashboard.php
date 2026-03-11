<?php

namespace App\Livewire\Admin;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Url;

class Dashboard extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    public function render(UserService $userService)
    {
        return view('livewire.admin.dashboard', [
            'users' => $userService->getPaginatedUsers(10, $this->search, $this->filterActive === 'active'),
            'stats' => $userService->getDashboardStats(),
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
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'role' => 'required|in:admin,client',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.letters' => 'A senha deve conter ao menos uma letra.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter ao menos um número.',
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
        } else {
            $userService->createUser($data);
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
                $userService->updateUser($this->confirmingUserId, ['is_active' => !$user->is_active]);
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
