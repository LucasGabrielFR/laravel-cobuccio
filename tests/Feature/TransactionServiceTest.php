<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = app(TransactionService::class);
    }

    public function test_user_can_deposit_money()
    {
        $user = User::factory()->create(['balance' => 0]);

        $transaction = $this->transactionService->deposit($user, 50.00);

        $this->assertEquals(5000, $transaction->amount);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals($user->id, $transaction->receiver_id);
        $this->assertNull($transaction->sender_id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => 5000,
        ]);
    }

    public function test_deposit_must_be_greater_than_zero()
    {
        $user = User::factory()->create(['balance' => 0]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('O valor do depósito deve ser maior que zero.');

        $this->transactionService->deposit($user, 0);
    }

    public function test_user_can_transfer_money()
    {
        $sender = User::factory()->create(['balance' => 10000]); // R$ 100,00
        $receiver = User::factory()->create(['balance' => 0]);

        $transaction = $this->transactionService->transfer($sender, $receiver, 50.00);

        $this->assertEquals(5000, $transaction->amount);
        $this->assertEquals('transfer', $transaction->type);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals($sender->id, $transaction->sender_id);
        $this->assertEquals($receiver->id, $transaction->receiver_id);

        $this->assertDatabaseHas('users', [
            'id' => $sender->id,
            'balance' => 5000,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $receiver->id,
            'balance' => 5000,
        ]);
    }

    public function test_cannot_transfer_money_without_sufficient_balance()
    {
        $sender = User::factory()->create(['balance' => 1000]); // R$ 10,00
        $receiver = User::factory()->create(['balance' => 0]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo insuficiente para realizar esta transferência.');

        $this->transactionService->transfer($sender, $receiver, 50.00);
    }

    public function test_cannot_transfer_to_self()
    {
        $sender = User::factory()->create(['balance' => 10000]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível transferir para você mesmo.');

        $this->transactionService->transfer($sender, $sender, 50.00);
    }

    public function test_can_request_reversal()
    {
        $sender = User::factory()->create(['balance' => 10000]);
        $receiver = User::factory()->create(['balance' => 0]);
        $transaction = $this->transactionService->transfer($sender, $receiver, 50.00);

        $reversal = $this->transactionService->requestReversal($transaction->id, $sender->id, 'Motivo de teste');

        $this->assertEquals('requested', $reversal->reversal_status);
        $this->assertEquals('Motivo de teste', $reversal->reversal_reason);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'reversal_status' => 'requested',
            'reversal_reason' => 'Motivo de teste',
        ]);
    }

    public function test_can_approve_reversal()
    {
        $sender = User::factory()->create(['balance' => 10000]);
        $receiver = User::factory()->create(['balance' => 0]);
        $originalTransaction = $this->transactionService->transfer($sender, $receiver, 50.00);

        $this->transactionService->requestReversal($originalTransaction->id, $sender->id, 'Motivo de teste');
        
        $this->transactionService->approveReversal($originalTransaction->id);

        $this->assertDatabaseHas('users', [
            'id' => $sender->id,
            'balance' => 10000, // Balance restored
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $receiver->id,
            'balance' => 0, // Reduced balance from 5000 to 0
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $originalTransaction->id,
            'reversal_status' => 'approved',
        ]);

        $this->assertDatabaseHas('transactions', [
            'sender_id' => $receiver->id,
            'receiver_id' => $sender->id,
            'amount' => 5000,
            'type' => 'transfer',
            'notes' => 'Estorno da transação #' . $originalTransaction->id,
            'related_transaction_id' => $originalTransaction->id,
        ]);
    }

    public function test_can_reject_reversal()
    {
        $sender = User::factory()->create(['balance' => 10000]);
        $receiver = User::factory()->create(['balance' => 0]);
        $transaction = $this->transactionService->transfer($sender, $receiver, 50.00);

        $this->transactionService->requestReversal($transaction->id, $sender->id, 'Motivo de teste');
        
        $this->transactionService->rejectReversal($transaction->id);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'reversal_status' => 'rejected',
        ]);
    }

    public function test_can_request_deposit_reversal()
    {
        $user = User::factory()->create(['balance' => 0]);
        $transaction = $this->transactionService->deposit($user, 50.00);

        $reversal = $this->transactionService->requestReversal($transaction->id, $user->id, 'Estorno de depósito');

        $this->assertEquals('requested', $reversal->reversal_status);
        $this->assertEquals('Estorno de depósito', $reversal->reversal_reason);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'reversal_status' => 'requested',
        ]);
    }

    public function test_can_approve_deposit_reversal()
    {
        $user = User::factory()->create(['balance' => 5000]);
        $transaction = $this->transactionService->deposit($user, 100.00);

        $this->transactionService->requestReversal($transaction->id, $user->id, 'Estorno de depósito');
        
        $this->transactionService->approveReversal($transaction->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => 5000, // Balance restored to previous amount (5000 + 10000 - 10000)
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'reversal_status' => 'approved',
        ]);

        $this->assertDatabaseHas('transactions', [
            'sender_id' => $user->id,
            'receiver_id' => null,
            'amount' => 10000,
            'type' => 'reversal',
            'related_transaction_id' => $transaction->id,
        ]);
    }
}
