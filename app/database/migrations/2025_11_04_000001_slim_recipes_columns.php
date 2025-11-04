<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // CASE A: both columns exist => drop the typo one
        if (Schema::hasColumn('recipes', 'instructure') && Schema::hasColumn('recipes', 'instructions')) {
            // Drop the typo column
            DB::statement("ALTER TABLE recipes DROP COLUMN instructure");
        }
        // CASE B: only instructure exists => rename it to instructions
        elseif (Schema::hasColumn('recipes', 'instructure') && !Schema::hasColumn('recipes', 'instructions')) {
            DB::statement("ALTER TABLE recipes CHANGE instructure instructions TEXT NULL");
        }
        // CASE C: neither exists => add correct column
        elseif (!Schema::hasColumn('recipes', 'instructions')) {
            DB::statement("ALTER TABLE recipes ADD COLUMN instructions TEXT NULL");
        }

        // Make legacy/unused columns nullable so inserts don’t fail
        if (Schema::hasColumn('recipes', 'title')) {
            DB::statement("ALTER TABLE recipes MODIFY title VARCHAR(255) NULL");
        }
        if (Schema::hasColumn('recipes', 'description')) {
            DB::statement("ALTER TABLE recipes MODIFY description TEXT NULL");
        }
    }

    public function down(): void
    {
        // No rollback needed for this cleanup.
    }
};
