<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Drop legacy columns if they exist
        Schema::table('goals', function (Blueprint $table) {
            if (Schema::hasColumn('goals', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('goals', 'direction')) {
                $table->dropColumn('direction');
            }
            // make sure timestamps exist
            if (!Schema::hasColumn('goals', 'created_at')) {
                $table->timestamps();
            }
        });

        // 2) Add new columns if missing (no Doctrine needed)
        Schema::table('goals', function (Blueprint $table) {
            if (!Schema::hasColumn('goals', 'metric')) {
                $table->enum('metric', [
                    'calories','protein','carbs','fat','fiber','sugar','sodium','carbon_footprint_gco2e'
                ])->after('user_id');
            }
            if (!Schema::hasColumn('goals', 'comparator')) {
                $table->enum('comparator', ['at_most','at_least'])->default('at_most');
            }
            if (!Schema::hasColumn('goals', 'period')) {
                $table->enum('period', ['daily','weekly','monthly'])->default('daily');
            }
        });

        // 3) Ensure types/constraints using raw SQL (avoids DBAL)
        // target_value -> DECIMAL(10,2) NOT NULL
        try {
            DB::statement("ALTER TABLE goals MODIFY target_value DECIMAL(10,2) NOT NULL");
        } catch (\Throwable $e) {
            // ignore if already correct
        }

        // user_id should already be BIGINT UNSIGNED with FK — if not, try to add FK safely
        // (If a FK already exists, this will fail silently and we’ll ignore.)
        try {
            DB::statement("ALTER TABLE goals ADD CONSTRAINT goals_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        } catch (\Throwable $e) {
            // ignore if exists
        }

        // 4) Unique constraint per (user_id, metric, period)
        // First, drop if an older unique exists with a different name
        try {
            DB::statement("ALTER TABLE goals DROP INDEX goals_user_metric_period_unique");
        } catch (\Throwable $e) { /* maybe not there — fine */ }

        try {
            DB::statement("CREATE UNIQUE INDEX goals_user_metric_period_unique ON goals (user_id, metric, period)");
        } catch (\Throwable $e) {
            // ignore if already created
        }
    }

    public function down(): void
    {
        // best-effort rollback of what we added
        try {
            DB::statement("ALTER TABLE goals DROP INDEX goals_user_metric_period_unique");
        } catch (\Throwable $e) {}

        Schema::table('goals', function (Blueprint $table) {
            if (Schema::hasColumn('goals', 'metric')) $table->dropColumn('metric');
            if (Schema::hasColumn('goals', 'comparator')) $table->dropColumn('comparator');
            if (Schema::hasColumn('goals', 'period')) $table->dropColumn('period');
            // leaving target_value as DECIMAL(10,2) (non-destructive)
        });
    }
};
