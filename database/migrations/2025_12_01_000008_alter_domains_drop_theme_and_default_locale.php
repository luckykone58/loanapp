<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            if (Schema::hasColumn('domains', 'theme')) {
                $table->dropColumn('theme');
            }
            if (Schema::hasColumn('domains', 'default_locale')) {
                $table->dropColumn('default_locale');
            }
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            if (!Schema::hasColumn('domains', 'theme')) {
                $table->string('theme')->nullable();
            }
            if (!Schema::hasColumn('domains', 'default_locale')) {
                $table->string('default_locale', 10)->default('en');
            }
        });
    }
};




