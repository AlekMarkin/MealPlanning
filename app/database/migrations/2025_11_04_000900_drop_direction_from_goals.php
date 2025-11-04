<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('goals', 'direction')) {
            // Drop without requiring doctrine/dbal
            DB::statement('ALTER TABLE goals DROP COLUMN direction');
        }
    }

    public function down(): void
    {
        // Optional restore (defaults to 'at_most'); adjust if you prefer null
        if (!Schema::hasColumn('goals', 'direction')) {
            DB::statement("ALTER TABLE goals ADD COLUMN direction VARCHAR(10) NOT NULL DEFAULT 'at_most'");
        }
    }
};
