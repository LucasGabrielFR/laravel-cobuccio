<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.auth.login');
    }

    public function login()
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Muitas tentativas de login. Tente novamente em {$seconds} segundos.",
            ]);
        }

        // Only allow active users to authenticate
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => true], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate(); // Prevent session fixation

            return redirect()->intended('/dashboard');
        }

        RateLimiter::hit($throttleKey);

        throw ValidationException::withMessages([
            'email' => 'As credenciais fornecidas estão incorretas ou o usuário está inativo.',
        ]);
    }
}
