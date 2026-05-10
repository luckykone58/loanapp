<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add "approved" to the enum list for withdrawals.status
        // Using DB::statement for MySQL ENUM alteration.
        // Adjust if using a different database that doesn't support ENUM or requires different syntax.
        if (Schema::hasTable('withdrawals')) {
            // Attempt to modify enum to include 'approved'
            try {
                DB::statement("ALTER TABLE `withdrawals` MODIFY `status` ENUM('processing','approved','fulfilled','rejected') NOT NULL DEFAULT 'processing'");
            } catch (\Throwable $e) {
                // Fallback for databases not supporting direct enum modification.
                // In such cases, developers may need to manually adjust the column type.
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('withdrawals')) {
            try {
                DB::statement("ALTER TABLE `withdrawals` MODIFY `status` ENUM('processing','fulfilled','rejected') NOT NULL DEFAULT 'processing'");
            } catch (\Throwable $e) {
                // No-op on failure
            }
        }
    }
};


