<?php

namespace App\Livewire\Auth;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\Rules\Password;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(), // Bonus: check if password has been leaked
            ],
        ];
    }

    protected function messages()
    {
        return [
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.letters' => 'A senha deve conter ao menos uma letra.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter ao menos um número.',
            'password.uncompromised' => 'Esta senha é muito comum e já apareceu em vazamentos de dados. Escolha algo mais seguro.',
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.auth.register');
    }

    public function register(UserService $userService)
    {
        $this->validate();

        $user = $userService->createUser([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => 'client', // Defaults to client role
            'is_active' => true,
        ]);

        Auth::login($user);
        session()->regenerate(); // Prevent session fixation

        return redirect()->intended('/dashboard');
    }
}
