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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('modified_date')->useCurrent()->useCurrentOnUpdate();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('loan_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->date('start_date');
            $table->integer('period')->comment('In months');
            $table->decimal('interest', 5, 2)->comment('Percentage rate');
            $table->enum('status', ['processing', 'approved', 'rejected', 'paid'])->default('processing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};