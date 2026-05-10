<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('notifications', function (Blueprint $table) {
			if (!Schema::hasColumn('notifications', 'title')) {
				$table->string('title')->nullable()->after('user_id');
			}
			if (!Schema::hasColumn('notifications', 'message')) {
				$table->string('message')->nullable()->after('title');
			}
			if (!Schema::hasColumn('notifications', 'subtext')) {
				$table->text('subtext')->nullable()->after('message');
			}
		});

		// Migrate existing data from 'text' to 'subtext' if 'text' exists
		if (Schema::hasColumn('notifications', 'text')) {
			DB::statement("UPDATE notifications SET subtext = COALESCE(subtext, text)");
			Schema::table('notifications', function (Blueprint $table) {
				$table->dropColumn('text');
			});
		}
	}

	public function down(): void
	{
		// Recreate 'text' column if missing and backfill from subtext
		if (!Schema::hasColumn('notifications', 'text')) {
			Schema::table('notifications', function (Blueprint $table) {
				$table->text('text')->nullable()->after('user_id');
			});
			DB::statement("UPDATE notifications SET text = COALESCE(text, subtext)");
		}

		Schema::table('notifications', function (Blueprint $table) {
			if (Schema::hasColumn('notifications', 'title')) {
				$table->dropColumn('title');
			}
			if (Schema::hasColumn('notifications', 'message')) {
				$table->dropColumn('message');
			}
			if (Schema::hasColumn('notifications', 'subtext')) {
				$table->dropColumn('subtext');
			}
		});
	}
};



