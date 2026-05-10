<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username first
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->after('name');
            }
        });

        // Ensure standalone index on domain_id for FK before dropping composite index
        $hasDomainIndex = collect(DB::select("SHOW INDEX FROM `users` WHERE Key_name = 'users_domain_id_index'"))->isNotEmpty();
        if (!$hasDomainIndex) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('domain_id', 'users_domain_id_index');
            });
        }

        // Adjust indices: drop old domain_id+email index if exists before dropping email
        $hasCompositeIndex = collect(DB::select("SHOW INDEX FROM `users` WHERE Key_name = 'users_domain_id_email_index'"))->isNotEmpty();
        if ($hasCompositeIndex) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_domain_id_email_index');
            });
        }
        $hasEmailUnique = collect(DB::select("SHOW INDEX FROM `users` WHERE Key_name = 'users_email_unique'"))->isNotEmpty();
        if ($hasEmailUnique) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_email_unique');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            // Drop email-related columns
            if (Schema::hasColumn('users', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Enforce uniqueness
            $table->unique(['domain_id', 'username'], 'users_domain_id_username_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove composite unique on domain_id+username
            try {
                $table->dropUnique('users_domain_id_username_unique');
            } catch (\Throwable $e) {
                // ignore
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Restore email columns
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Optionally restore old index
            try {
                $table->index(['domain_id', 'email'], 'users_domain_id_email_index');
            } catch (\Throwable $e) {
                // ignore
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Drop username column
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });

        // Optionally drop the single-domain_id index if we created it
        $hasDomainIndex = collect(DB::select("SHOW INDEX FROM `users` WHERE Key_name = 'users_domain_id_index'"))->isNotEmpty();
        if ($hasDomainIndex) {
            Schema::table('users', function (Blueprint $table) {
                try {
                    $table->dropIndex('users_domain_id_index');
                } catch (\Throwable $e) {
                    // ignore
                }
            });
        }
    }
};


