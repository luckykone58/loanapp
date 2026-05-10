<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('settings', 'value')) {
                $table->text('value')->nullable()->after('name');
            }
            if (!Schema::hasColumn('settings', 'domain_id')) {
                $table->foreignId('domain_id')->nullable()->after('id')->constrained('domains')->nullOnDelete();
            }
            $table->unique(['domain_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['domain_id', 'name']);
            if (Schema::hasColumn('settings', 'domain_id')) {
                $table->dropConstrainedForeignId('domain_id');
            }
            if (Schema::hasColumn('settings', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('settings', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};




