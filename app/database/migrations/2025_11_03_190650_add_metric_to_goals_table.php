<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            if (!Schema::hasColumn('goals', 'metric')) {
                $table->enum('metric', [
                    'calories','protein','carbs','fat','fiber','sugar','sodium','carbon_footprint'
                ])->default('calories')->after('user_id');
            }
            // keep existing 'direction' (string) and 'target_value' as-is
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            if (Schema::hasColumn('goals', 'metric')) {
                $table->dropColumn('metric');
            }
        });
    }
};
