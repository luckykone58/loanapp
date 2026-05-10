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
        Schema::create('users_info', function (Blueprint $table) {
            $table->id();
            // Link to the default 'users' table
            $table->foreignId('user_id')->constrained()->unique()->onDelete('cascade');
            $table->timestamp('modified_date')->useCurrent()->useCurrentOnUpdate();

            // Core Gating Fields (Required for Loan Application Access)
            $table->decimal('wallet', 10, 2)->default(0.00)->comment('Current available balance');
            $table->integer('credit_score')->nullable();
            $table->string('withdrawal_code', 6)->nullable();
            $table->string('full_name')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('id_card_front')->nullable()->comment('Path to image');
            $table->string('id_card_back')->nullable()->comment('Path to image');
            $table->string('id_card_selfie')->nullable()->comment('Path to image');
            $table->text('address')->nullable();
            $table->string('signature')->nullable()->comment('Path or base64 data');

            // Employment Info
            $table->string('company')->nullable();
            $table->text('company_address')->nullable();
            $table->string('position')->nullable();
            $table->string('seniority')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();

            // Emergency Contacts
            $table->string('contact_1_person')->nullable();
            $table->string('contact_1_phone')->nullable();
            $table->string('contact_1_relativity')->nullable();
            $table->string('contact_2_person')->nullable();
            $table->string('contact_2_phone')->nullable();
            $table->string('contact_2_relativity')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('bank_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_info');
    }
};