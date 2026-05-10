<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('domains', 'expired_date')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->date('expired_date')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('domains', 'expired_date')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->dropColumn('expired_date');
            });
        }
    }
};


