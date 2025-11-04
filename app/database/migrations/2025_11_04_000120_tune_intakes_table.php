<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create or reshape the intakes table to support food OR recipe entries by date
        if (!Schema::hasTable('intakes')) {
            Schema::create('intakes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->date('intake_date')->index();
                $table->unsignedBigInteger('food_id')->nullable()->index();
                $table->unsignedBigInteger('recipe_id')->nullable()->index();
                $table->integer('quantity_g')->default(0);     // for foods (grams)
                $table->decimal('servings', 8, 2)->default(0); // for recipes (whole recipe multiples)
                $table->timestamps();
            });
        } else {
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
        }
    }

    public function down(): void
    {
        // Non-destructive: just drop table (we can export if needed)
        Schema::dropIfExists('intakes');
    }
};
