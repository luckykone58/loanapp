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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_date')->useCurrent();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('text');
            $table->enum('status', ['unread', 'read'])->default('unread');
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_date')->useCurrent();
            $table->decimal('amount', 10, 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['processing', 'fulfilled', 'rejected'])->default('processing');
            $table->string('withdraw_name')->nullable()->comment('Name on the bank account');
            $table->string('withdraw_number')->nullable()->comment('Bank account number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('withdrawals');
    }
};