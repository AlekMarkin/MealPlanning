<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('intakes') && Schema::hasColumn('intakes', 'consumed_at')) {
            // Backfill any NULL / zero dates using intake_date @ 12:00:00
            DB::statement("
                UPDATE `intakes`
                SET `consumed_at` = CONCAT(`intake_date`, ' 12:00:00')
                WHERE `consumed_at` IS NULL OR `consumed_at` = '0000-00-00 00:00:00'
            ");

            // Make it nullable so future inserts can set it explicitly (no doctrine/dbal needed)
            DB::statement("ALTER TABLE `intakes` MODIFY `consumed_at` DATETIME NULL");
        }
    }

    public function down(): void
    {
        // No-op (we don't want to reintroduce NOT NULL without default)
    }
};
