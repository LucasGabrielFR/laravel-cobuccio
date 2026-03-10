<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable()->constrained('users'); // NULL para depósitos
            $table->foreignId('receiver_id')->constrained('users');
            $table->bigInteger('amount'); // em centavos
            $table->enum('type', ['deposit', 'transfer', 'reversal']);
            $table->enum('status', ['pending', 'completed', 'reversed'])->default('completed');
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions'); // para rastrear o que foi estornado
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
