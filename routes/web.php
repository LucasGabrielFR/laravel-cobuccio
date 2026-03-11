<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
    // Redirecionamento inteligente baseado no perfil
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('client.wallet');
    })->name('dashboard');

    // Rotas de Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
    });

    // Rotas de Cliente
    Route::middleware('role:client')->group(function () {
        Route::get('/client/wallet', \App\Livewire\Client\Wallet::class)->name('client.wallet');
    });
});
