<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop a potential global unique on username if present
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_username_unique');
            });
        } catch (\Throwable $e) {
            // ignore if it doesn't exist
        }

        // Ensure composite unique (domain_id, username)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique(['domain_id', 'username'], 'users_domain_id_username_unique');
            });
        } catch (\Throwable $e) {
            // ignore if already exists
        }
    }

    public function down(): void
    {
        // Drop composite unique
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_domain_id_username_unique');
            });
        } catch (\Throwable $e) {
            // ignore
        }
        // Optionally restore global unique (not recommended)
        // try {
        //     Schema::table('users', function (Blueprint $table) {
        //         $table->unique('username');
        //     });
        // } catch (\Throwable $e) {
        //     // ignore
        // }
    }
};



