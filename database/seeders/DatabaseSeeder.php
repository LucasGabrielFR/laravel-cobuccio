<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Criar o Administrador
        User::factory()->create([
            'name' => 'Administrador Cobuccio',
            'email' => 'admin@wallet.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 2. Criar Clientes Diversos
        $alice = User::factory()->create([
            'name' => 'Alice da Silva',
            'email' => 'alice@wallet.com',
            'password' => bcrypt('senha123'),
            'role' => 'client',
            'is_active' => true,
        ]);

        $bob = User::factory()->create([
            'name' => 'Roberto Carlos',
            'email' => 'bob@wallet.com',
            'password' => bcrypt('senha123'),
            'role' => 'client',
            'is_active' => true,
        ]);

        $charlie = User::factory()->create([
            'name' => 'Carlos Magno',
            'email' => 'charlie@wallet.com',
            'password' => bcrypt('senha123'),
            'role' => 'client',
            'is_active' => true,
        ]);

        // 3. Simular Transações
        $transactionService = app(\App\Services\TransactionService::class);

        // Depósitos Iniciais
        $transactionService->deposit($alice, 500.00); // 500 reais
        $transactionService->deposit($bob, 150.00); // 150 reais
        $transactionService->deposit($charlie, 3000.00); // 3000 reais

        // Algumas Transferências
        $transactionService->transfer($alice, $bob, 50.00);
        $transactionService->transfer($charlie, $alice, 200.00);
        $transactionService->transfer($charlie, $bob, 500.00);
        
        // Simular uma solicitação de estorno (Bob pede estorno de algo que ele mandou para a Alice)
        $txForReversal = $transactionService->transfer($bob, $alice, 30.00);
        $transactionService->requestReversal($txForReversal->id, $bob->id, 'Enviei esse valor duplicado sem querer!');
    }
}
