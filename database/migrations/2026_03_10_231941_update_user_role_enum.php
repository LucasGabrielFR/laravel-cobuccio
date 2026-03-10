<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->change();
        });

        // Update existing data
        \DB::table('users')->where('role', 'user')->update(['role' => 'client']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'client'])->default('client')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
