<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ensure new columns exist
        if (Schema::hasTable('intakes')) {
            Schema::table('intakes', function (Blueprint $table) {
                if (!Schema::hasColumn('intakes', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->index()->after('id');
                }
                if (!Schema::hasColumn('intakes', 'intake_date')) {
                    $table->date('intake_date')->index()->after('user_id');
                }
                if (!Schema::hasColumn('intakes', 'food_id')) {
                    $table->unsignedBigInteger('food_id')->nullable()->index()->after('intake_date');
                }
                if (!Schema::hasColumn('intakes', 'recipe_id')) {
                    $table->unsignedBigInteger('recipe_id')->nullable()->index()->after('food_id');
                }
                if (!Schema::hasColumn('intakes', 'quantity_g')) {
                    $table->integer('quantity_g')->default(0)->after('recipe_id');
                }
                if (!Schema::hasColumn('intakes', 'servings')) {
                    $table->decimal('servings', 8, 2)->default(0)->after('quantity_g');
                }
                if (!Schema::hasColumn('intakes', 'created_at')) {
                    $table->timestamps();
                }
            });

            // If legacy `grams` column exists, make it harmless (nullable + default 0) or drop it.
            if (Schema::hasColumn('intakes', 'grams')) {
                // First, backfill quantity_g from grams where quantity_g is null/0 (defensive)
                DB::statement("UPDATE `intakes` SET `quantity_g` = COALESCE(`quantity_g`, 0) + COALESCE(`grams`, 0)");

                // Make grams nullable with default 0 to satisfy strict mode, then drop if you want.
                // Using raw SQL to avoid the need for doctrine/dbal.
                DB::statement("ALTER TABLE `intakes` MODIFY `grams` INT NULL DEFAULT 0");

                // Optionally drop the legacy column. Uncomment if you want it gone:
                // DB::statement("ALTER TABLE `intakes` DROP COLUMN `grams`");
            }
        }
    }

    public function down(): void
    {
        // Non-destructive down: do nothing (we don't want to recreate the bad legacy column).
    }
};
